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
 * @author     David Molineus <david.molineus@netzmacht.de>
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

class StylesheetRendererSubscriber extends AbstractRendererSubscriber implements EventSubscriberInterface
{
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

    /**
     * Compile assets.
     *
     * @param CompileAssetEvent        $event           The subscribed event.
     * @param string                   $eventName       The event name.
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function compileAsset(CompileAssetEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
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

    /**
     * Render designer mode html.
     *
     * @param RenderAssetHtmlEvent $event The subscribed event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function renderDesignerModeHtml(RenderAssetHtmlEvent $event)
    {
        if (!$event->getHtml() && $event->isDesignerMode()) {
            $asset = $event->getAsset();

            if (!$asset instanceof ExtendedAssetInterface || !$asset->isInline()) {
                // session id
                $sessionId  = $this->storeInSession($asset);
                $realAssets = $this->getRealAssets($asset);

                // overwrite the target path
                $url = $this->getDesignerModeProxyUrl('css', $realAssets, $sessionId);
                $asset->setTargetPath($url);

                // remember asset for debug tool
                $this->developerTool->registerFile(
                    $sessionId,
                    (object) [
                        'asset' => $realAssets,
                        'type'  => 'css',
                        'url'   => $url,
                    ]
                );

                // add debug information
                $html  = $this->developerTool->getDebugComment($asset);
                $html .= $this->generateLinkElement($asset, $url, $sessionId);

                $event->setHtml($html);
            }
        }
    }

    /**
     * Render designer mode inline html.
     *
     * @param RenderAssetHtmlEvent $event The subscribed event.
     *
     * @return void
     */
    public function renderDesignerModeInlineHtml(RenderAssetHtmlEvent $event)
    {
        if (!$event->getHtml() && $event->isDesignerMode()) {
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

                // generate html
                // add debug information
                $html  = $this->developerTool->getDebugComment($asset);
                $html .= $this->renderStyleElement($asset, $css);

                $event->setHtml($html);
            }
        }
    }

    /**
     * Render link html.
     *
     * @param RenderAssetHtmlEvent     $event           The subscribed event.
     * @param string                   $eventName       The event name.
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher.
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
                $linkHtml = $this->generateLinkElement($asset, $targetUrl);

                $event->setHtml($linkHtml);
            }
        }
    }

    /**
     * Render inline html.
     *
     * @param RenderAssetHtmlEvent $event The subscribed event.
     *
     * @return void
     */
    public function renderInlineHtml(RenderAssetHtmlEvent $event)
    {
        if (!$event->getHtml() && !$event->isDesignerMode()) {
            $asset = $event->getAsset();

            if ($asset instanceof ExtendedAssetInterface && $asset->isInline()) {
                // overwrite the target path
                $asset->setTargetPath($this->getTargetPath());

                // load and dump the collection
                $asset->load($event->getDefaultFilters());

                $css       = $asset->dump($event->getDefaultFilters());
                $styleHtml = $this->renderStyleElement($asset, $css);

                $event->setHtml($styleHtml);
            }
        }
    }

    /**
     * Render style element.
     *
     * @param $asset
     * @param $css
     *
     * @return string
     */
    private function renderStyleElement($asset, $css)
    {
        $page = $this->pageProvider->getPage();

        // html mode
        $xhtml = ($page->outputFormat == 'xhtml');

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

        return $styleHtml;
    }

    /**
     * @param $asset
     * @param $url
     * @param $elementId
     *
     * @return string
     */
    private function generateLinkElement($asset, $url, $elementId = null)
    {
        // html mode
        $xhtml = ($this->pageProvider->getPage()->outputFormat == 'xhtml');

        // generate html
        $linkHtml = '<link';

        if ($elementId) {
            $linkHtml .= sprintf(' id="%s"', $elementId);
        }

        $linkHtml .= sprintf(' href="%s"', $url);
        if ($xhtml) {
            $linkHtml .= ' type="text/css"';
        }
        $linkHtml .= ' rel="stylesheet"';
        if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
            $linkHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
        }
        $linkHtml .= ($xhtml ? ' />' : '>');

        // wrap cc around
        if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
            $linkHtml = ThemePlusUtils::wrapCc($linkHtml, $asset->getConditionalComment());
        }

        $linkHtml .= PHP_EOL;

        return $linkHtml;
    }
}
