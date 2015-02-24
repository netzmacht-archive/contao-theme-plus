<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

namespace Bit3\Contao\ThemePlus\Renderer;

use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Bit3\Contao\ThemePlus\Asset\DelegatorAssetInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use Bit3\Contao\ThemePlus\Event\AddStaticDomainEvent;
use Bit3\Contao\ThemePlus\Event\CompileAssetEvent;
use Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Bit3\Contao\ThemePlus\ThemePlusEvents;
use Bit3\Contao\ThemePlus\ThemePlusUtils;
use DependencyInjection\Container\PageProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JavaScriptRendererSubscriber implements EventSubscriberInterface
{
    /**
     * @var PageProvider
     */
    private $pageProvider;

    /**
     * @var DeveloperTool
     */
    private $developerTool;

    public function __construct(PageProvider $pageProvider, DeveloperTool $developerTool)
    {
        $this->pageProvider  = $pageProvider;
        $this->developerTool = $developerTool;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ThemePlusEvents::COMPILE_JAVASCRIPT     => [
                ['compileAsset']
            ],
            ThemePlusEvents::RENDER_JAVASCRIPT_HTML => [
                ['renderDesignerModeHtml'],
                ['renderDesignerModeInlineHtml'],
                ['renderLinkHtml'],
                ['renderInlineHtml'],
            ],
        ];
    }

    public function compileAsset(
        CompileAssetEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (!$event->getTargetPath()) {
            $asset = $event->getAsset();

            $generateAssetPathEvent = new GenerateAssetPathEvent(
                $event->getRenderMode(),
                $event->getPage(),
                $event->getLayout(),
                $asset,
                $event->getDefaultFilters(),
                'js'
            );
            $eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

            $targetPath = $generateAssetPathEvent->getPath();

            if ($event->isOverwrite() || !file_exists(TL_ROOT . DIRECTORY_SEPARATOR . $targetPath)) {
                // overwrite the target path
                $asset->setTargetPath($targetPath);

                // load and dump the collection
                $asset->load($event->getDefaultFilters());
                $contents = $asset->dump($event->getDefaultFilters());

                // write the asset
                file_put_contents($targetPath, $contents);
            }

            $event->setTargetPath($targetPath);
        }
    }

    public function renderDesignerModeHtml(
        RenderAssetHtmlEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (!$event->getHtml() && $event->isDesignerMode()) {
            $asset = $event->getAsset();

            if (!$asset instanceof ExtendedAssetInterface || !$asset->isInline()) {
                $page = $this->pageProvider->getPage();
                $html = '';

                // html mode
                $xhtml = ($page->outputFormat == 'xhtml');

                // session id
                $id = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);

                // get the session object
                $session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

                if (!$session || $asset->getLastModified() > $session->asset->getLastModified()) {
                    $session        = new \stdClass;
                    $session->page  = $page->id;
                    $session->asset = $asset;

                    $_SESSION['THEME_PLUS_ASSETS'][$id] = serialize($session);
                }

                $realAssets = $asset;
                while ($realAssets instanceof DelegatorAssetInterface) {
                    $realAssets = $realAssets->getAsset();
                }

                if ($realAssets instanceof FileAsset) {
                    $name = basename($realAssets->getSourcePath());
                } else {
                    if ($realAssets instanceof HttpAsset) {
                        $class    = new \ReflectionClass('Assetic\Asset\HttpAsset');
                        $property = $class->getProperty('sourceUrl');
                        $property->setAccessible(true);
                        $url  = $property->getValue($realAssets);
                        $name = 'url_' . basename(parse_url($url, PHP_URL_PATH));
                    } else {
                        $name = 'asset_' . $id;
                    }
                }

                // generate the proxy url
                $url = sprintf(
                    'assets/theme-plus/proxy.php/js/%s/%s',
                    $id,
                    $name
                );

                // overwrite the target path
                $asset->setTargetPath($url);

                // remember asset for debug tool
                $this->developerTool->registerFile(
                    $id,
                    (object) [
                        'asset' => $realAssets,
                        'type'  => 'js',
                        'url'   => $url,
                    ]
                );

                // generate html
                if ($event->getLayout()->theme_plus_javascript_lazy_load) {
                    $scriptHtml = '<script';
                    $scriptHtml .= sprintf(' id="%s"', $id);
                    if ($xhtml) {
                        $scriptHtml .= ' type="text/javascript"';
                    }
                    $scriptHtml .= '>';
                    $scriptHtml .= sprintf(
                        'window.loadAsync(%s, %s)',
                        json_encode($url),
                        json_encode($id)
                    );
                    $scriptHtml .= '</script>';
                } else {
                    $scriptHtml = '<script';
                    $scriptHtml .= sprintf(' id="%s"', $id);
                    $scriptHtml .= sprintf(' src="%s"', $url);
                    if ($xhtml) {
                        $scriptHtml .= ' type="text/javascript"';
                    }
                    $scriptHtml .= sprintf(
                        ' onload="window.themePlusDevTool && window.themePlusDevTool.triggerAsyncLoad(this, \'%s\');"',
                        $id
                    );
                    $scriptHtml .= '></script>';
                }

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
                }

                // add debug information
                $html .= $this->developerTool->getDebugComment($asset);

                $html .= $scriptHtml . PHP_EOL;

                $event->setHtml($html);
            }
        }
    }

    public function renderDesignerModeInlineHtml(
        RenderAssetHtmlEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (!$event->getHtml() && $event->isDesignerMode()) {
            $asset = $event->getAsset();

            if ($asset instanceof ExtendedAssetInterface && $asset->isInline()) {
                $page = $this->pageProvider->getPage();
                $html = '';

                // html mode
                $xhtml = ($page->outputFormat == 'xhtml');

                // retrieve page path
                $targetPath = \Environment::get('requestUri');
                // remove query string
                $targetPath = preg_replace('~\?\.*~', '', $targetPath);
                // remove leading /
                $targetPath = ltrim($targetPath, '/');

                // overwrite the target path
                $asset->setTargetPath($targetPath);

                // load and dump the collection
                $asset->load($event->getDefaultFilters());
                $js = $asset->dump($event->getDefaultFilters());

                // generate html
                $scriptHtml = '<script';
                if ($xhtml) {
                    $scriptHtml .= ' type="text/javascript"';
                }
                $scriptHtml .= '>';
                $scriptHtml .= $js;
                $scriptHtml .= '</script>';

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
                }

                // add debug information
                $html .= $this->developerTool->getDebugComment($asset);

                $html .= $scriptHtml . PHP_EOL;

                $event->setHtml($html);
            }
        }
    }

    public function renderLinkHtml(RenderAssetHtmlEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
        if (!$event->getHtml() && !$event->isDesignerMode()) {
            $asset = $event->getAsset();

            if (!$asset instanceof ExtendedAssetInterface || !$asset->isInline()) {
                $compileEvent = new CompileAssetEvent(
                    $event->getRenderMode(),
                    $event->getPage(),
                    $event->getLayout(),
                    $asset,
                    $event->getDefaultFilters()
                );
                $eventDispatcher->dispatch(ThemePlusEvents::COMPILE_JAVASCRIPT, $compileEvent);

                $targetPath = $compileEvent->getTargetPath();

                $addStaticDomainEvent = new AddStaticDomainEvent(
                    $event->getRenderMode(),
                    $event->getPage(),
                    $event->getLayout(),
                    $targetPath
                );
                $eventDispatcher->dispatch(ThemePlusEvents::ADD_STATIC_DOMAIN, $addStaticDomainEvent);
                $targetUrl = $addStaticDomainEvent->getUrl();

                // html mode
                $xhtml = ($event->getPage()->outputFormat == 'xhtml');

                // generate html
                if ($event->getLayout()->theme_plus_javascript_lazy_load) {
                    $scriptHtml = '<script';
                    if ($xhtml) {
                        $scriptHtml .= ' type="text/javascript"';
                    }
                    $scriptHtml .= '>';
                    $scriptHtml .= sprintf(
                        'window.loadAsync(%s)',
                        json_encode($targetUrl)
                    );
                    $scriptHtml .= '</script>';
                } else {
                    $scriptHtml = '<script';
                    $scriptHtml .= sprintf(' src="%s"', $targetUrl);
                    if ($xhtml) {
                        $scriptHtml .= ' type="text/javascript"';
                    }
                    $scriptHtml .= '></script>';
                }
                $scriptHtml .= PHP_EOL;

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
                }

                $event->setHtml($scriptHtml);
            }
        }
    }

    public function renderInlineHtml(RenderAssetHtmlEvent $event)
    {
        if (!$event->getHtml() && !$event->isDesignerMode()) {
            $asset = $event->getAsset();

            if ($asset instanceof ExtendedAssetInterface && $asset->isInline()) {
                $page = $this->pageProvider->getPage();

                // retrieve page path
                $targetPath = \Environment::get('requestUri');
                // remove query string
                $targetPath = preg_replace('~\?\.*~', '', $targetPath);
                // remove leading /
                $targetPath = ltrim($targetPath, '/');

                // overwrite the target path
                $asset->setTargetPath($targetPath);

                // load and dump the collection
                $asset->load($event->getDefaultFilters());
                $js = $asset->dump($event->getDefaultFilters());

                // html mode
                $xhtml = ($page->outputFormat == 'xhtml');

                // generate html
                $scriptHtml = '<script';
                if ($xhtml) {
                    $scriptHtml .= ' type="text/javascript"';
                }
                $scriptHtml .= '>';
                $scriptHtml .= $js;
                $scriptHtml .= '</script>';
                $scriptHtml .= PHP_EOL;

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
                }

                $event->setHtml($scriptHtml);
            }
        }
    }
}
