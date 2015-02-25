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
use Bit3\Contao\ThemePlus\Event\AddStaticDomainEvent;
use Bit3\Contao\ThemePlus\Event\CompileAssetEvent;
use Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Bit3\Contao\ThemePlus\ThemePlusEvents;
use Bit3\Contao\ThemePlus\ThemePlusUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JavaScriptRendererSubscriber extends AbstractRendererSubscriber implements EventSubscriberInterface
{
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

    /**
     * @param CompileAssetEvent        $event
     * @param                          $eventName
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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

    /**
     * @param RenderAssetHtmlEvent     $event
     * @param                          $eventName
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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

                $sessionId  = $this->storeInSession($asset);
                $realAssets = $this->getRealAssets($asset);
                $url        = $this->getDesignerModeProxyUrl('js', $realAssets, $sessionId);

                // overwrite the target path
                $asset->setTargetPath($url);

                // remember asset for debug tool
                $this->developerTool->registerFile(
                    $sessionId,
                    (object) [
                        'asset' => $realAssets,
                        'type'  => 'js',
                        'url'   => $url,
                    ]
                );

                // generate html
                $scriptHtml = $this->renderDesignerModelScriptTag($event, $sessionId, $xhtml, $url);

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

    /**
     * @param RenderAssetHtmlEvent     $event
     * @param                          $eventName
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
                $javascript = $asset->dump($event->getDefaultFilters());

                // generate html
                $scriptHtml = '<script';
                if ($xhtml) {
                    $scriptHtml .= ' type="text/javascript"';
                }
                $scriptHtml .= '>';
                $scriptHtml .= $javascript;
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

    /**
     * @param RenderAssetHtmlEvent     $event
     * @param                          $eventName
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
                $targetUrl  = $addStaticDomainEvent->getUrl();
                $scriptHtml = $this->renderLinkScriptTag($event, $targetUrl, $asset);

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

                // overwrite the target path
                $asset->setTargetPath($this->getTargetPath());

                // load and dump the collection
                $asset->load($event->getDefaultFilters());
                $javaScript = $asset->dump($event->getDefaultFilters());

                // html mode
                $xhtml = ($page->outputFormat == 'xhtml');

                // generate html
                $scriptHtml = '<script';
                if ($xhtml) {
                    $scriptHtml .= ' type="text/javascript"';
                }
                $scriptHtml .= '>';
                $scriptHtml .= $javaScript;
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

    /**
     * @param RenderAssetHtmlEvent $event
     * @param                      $sessionId
     * @param                      $xhtml
     * @param                      $url
     *
     * @return string
     */
    private function renderDesignerModelScriptTag(RenderAssetHtmlEvent $event, $sessionId, $xhtml, $url)
    {
        if ($event->getLayout()->theme_plus_javascript_lazy_load) {
            $scriptHtml = '<script';
            $scriptHtml .= sprintf(' id="%s"', $sessionId);
            if ($xhtml) {
                $scriptHtml .= ' type="text/javascript"';
            }
            $scriptHtml .= '>';
            $scriptHtml .= sprintf(
                'window.loadAsync(%s, %s)',
                json_encode($url),
                json_encode($sessionId)
            );
            $scriptHtml .= '</script>';
        } else {
            $scriptHtml = '<script';
            $scriptHtml .= sprintf(' id="%s"', $sessionId);
            $scriptHtml .= sprintf(' src="%s"', $url);
            if ($xhtml) {
                $scriptHtml .= ' type="text/javascript"';
            }
            $scriptHtml .= sprintf(
                ' onload="window.themePlusDevTool && window.themePlusDevTool.triggerAsyncLoad(this, \'%s\');"',
                $sessionId
            );
            $scriptHtml .= '></script>';
        }

        return $scriptHtml;
    }

    /**
     * @param RenderAssetHtmlEvent $event
     * @param                      $targetUrl
     * @param                      $asset
     *
     * @return string
     */
    private function renderLinkScriptTag(RenderAssetHtmlEvent $event, $targetUrl, $asset)
    {
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

        return $scriptHtml;
    }
}
