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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StylesheetRendererSubscriber implements EventSubscriberInterface
{
    /**
     * @var DeveloperTool
     */
    private $developerTool;

    public function __construct(DeveloperTool $developerTool)
    {
        $this->developerTool = $developerTool;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ThemePlusEvents::COMPILE_STYLESHEET     => [
                ['compileAsset']
            ],
            ThemePlusEvents::RENDER_STYLESHEET_HTML => [
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
                'css'
            );
            $eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

            $targetPath = $generateAssetPathEvent->getPath();

            if ($event->isOverwrite() || !file_exists(TL_ROOT . DIRECTORY_SEPARATOR . $targetPath)) {
                // overwrite the target path
                $asset->setTargetPath($targetPath);

                // load and dump the collection
                $asset->load($event->getDefaultFilters());
                $css = $asset->dump($event->getDefaultFilters());

                // write the asset
                file_put_contents($targetPath, $css);
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
                global $objPage;

                $html = '';

                // html mode
                $xhtml     = ($objPage->outputFormat == 'xhtml');
                $tagEnding = $xhtml ? ' />' : '>';

                // session id
                $id = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);

                // get the session object
                $session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

                if (!$session || $asset->getLastModified() > $session->asset->getLastModified()) {
                    $session        = new \stdClass;
                    $session->page  = $objPage->id;
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
                        $class    = new \ReflectionClass($realAssets);
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
                    'assets/theme-plus/proxy.php/css/%s/%s',
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
                        'type'  => 'css',
                        'url'   => $url,
                    ]
                );

                // generate html
                $linkHtml = '<link';
                $linkHtml .= sprintf(' id="%s"', $id);
                $linkHtml .= sprintf(' href="%s"', $url);
                if ($xhtml) {
                    $linkHtml .= ' type="text/css"';
                }
                $linkHtml .= ' rel="stylesheet"';
                if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
                    $linkHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
                }
                $linkHtml .= $tagEnding;

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $linkHtml = ThemePlusUtils::wrapCc($linkHtml, $asset->getConditionalComment());
                }

                // add debug information
                $html .= $this->developerTool->getDebugComment($asset);

                $html .= $linkHtml . PHP_EOL;

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
                global $objPage;

                $html = '';

                // html mode
                $xhtml = ($objPage->outputFormat == 'xhtml');

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
                $css = $asset->dump($event->getDefaultFilters());

                // generate html
                $styleHtml = '<style';
                if ($xhtml) {
                    $styleHtml .= ' type="text/css"';
                }
                if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
                    $styleHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
                }
                $styleHtml .= '>';
                $styleHtml .= $css;
                $styleHtml .= '</style>';

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $styleHtml = ThemePlusUtils::wrapCc($styleHtml, $asset->getConditionalComment());
                }

                // add debug information
                $html .= $developerTools->getDebugComment($asset);

                $html .= $styleHtml . PHP_EOL;

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
                $eventDispatcher->dispatch(ThemePlusEvents::COMPILE_STYLESHEET, $compileEvent);

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
                $xhtml     = ($event->getPage()->outputFormat == 'xhtml');
                $tagEnding = $xhtml ? ' />' : '>';

                // generate html
                $linkHtml = '<link';
                $linkHtml .= sprintf(' href="%s"', $targetUrl);
                if ($xhtml) {
                    $linkHtml .= ' type="text/css"';
                }
                $linkHtml .= ' rel="stylesheet"';
                if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
                    $linkHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
                }
                $linkHtml .= $tagEnding;

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $linkHtml = ThemePlusUtils::wrapCc($linkHtml, $asset->getConditionalComment());
                }

                $linkHtml .= PHP_EOL;

                $event->setHtml($linkHtml);
            }
        }
    }

    public function renderInlineHtml(RenderAssetHtmlEvent $event)
    {
        if (!$event->getHtml() && !$event->isDesignerMode()) {
            $asset = $event->getAsset();

            if ($asset instanceof ExtendedAssetInterface && $asset->isInline()) {
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
                $css = $asset->dump($event->getDefaultFilters());

                global $objPage;

                // html mode
                $xhtml = ($objPage->outputFormat == 'xhtml');

                // generate html
                $styleHtml = '<style';
                if ($xhtml) {
                    $styleHtml .= ' type="text/css"';
                }
                if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
                    $styleHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
                }
                $styleHtml .= '>';
                $styleHtml .= $css;
                $styleHtml .= '</style>';

                // wrap cc around
                if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
                    $styleHtml = ThemePlusUtils::wrapCc($styleHtml, $asset->getConditionalComment());
                }

                $styleHtml .= PHP_EOL;

                $event->setHtml($styleHtml);
            }
        }
    }
}
