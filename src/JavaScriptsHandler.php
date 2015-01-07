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

namespace Bit3\Contao\ThemePlus;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Filter\FilterInterface;
use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Event\GeneratePreCompiledAssetsCacheEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Bit3\Contao\ThemePlus\Event\OrganizePreCompiledAssetsEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use Doctrine\Common\Cache\Cache;
use FrontendTemplate;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Template;

/**
 * Class JavaScriptsHandler
 */
class JavaScriptsHandler
{
    /**
     * @see \Contao\Template::parse
     *
     * @param \Template $template
     */
    public function hookParseTemplate(Template $template)
    {
        if ($template instanceof FrontendTemplate) {
            if (substr($template->getName(), 0, 3) == 'fe_') {
                $template->mootools = '[[TL_SCRIPTS]]' . "\n" . $template->mootools;
            }
        }
    }

    /**
     * Replace dynamic script tags.
     *
     * @see \Contao\Controller::replaceDynamicScriptTags
     *
     * @param string $buffer
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function hookReplaceDynamicScriptTags($buffer)
    {
        global $objPage;

        if ($objPage) {
            $headScripts = '';
            $bodyScripts = '';

            // search for the layout
            $layout = \LayoutModel::findByPk($objPage->layout);

            /** @var RenderModeDeterminer $renderModeDeterminer */
            $renderModeDeterminer = $GLOBALS['container']['theme-plus-render-mode-determiner'];

            $renderMode = $renderModeDeterminer->determineMode();

            if (RenderMode::PRE_COMPILE == $renderMode) {
                // pre-compile javascripts
                $this->compileJavaScripts(
                    $objPage,
                    $layout
                );
            } elseif (
                !$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']
                && RenderMode::LIVE == $renderMode
            ) {
                // load cached javascripts
                $this->loadJavaScripts(
                    $objPage,
                    $layout,
                    $headScripts,
                    $bodyScripts
                );
            } else {
                // dynamically parse javascripts
                $this->parseJavaScripts(
                    $renderMode,
                    $objPage,
                    $layout,
                    $headScripts,
                    $bodyScripts
                );
            }

            $GLOBALS['TL_JAVASCRIPT'] = [];

            // replace dynamic scripts
            return str_replace(
                ['[[TL_HEAD]]', '[[TL_SCRIPTS]]'],
                [$headScripts, $bodyScripts],
                $buffer
            );
        }

        return $buffer;
    }

    /**
     * Pre-compile javascripts.
     *
     * @param \PageModel   $page
     * @param \LayoutModel $layout
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function compileJavaScripts(\PageModel $page, \LayoutModel $layout)
    {
        /** @var Cache $cache */
        $cache = $GLOBALS['container']['theme-plus-assets-cache'];

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $defaultFilters = $this->getDefaultFilters(RenderMode::PRE_COMPILE, $layout);

        /** @var AssetCollectionInterface $headAssets */
        /** @var AssetCollectionInterface $bodyAssets */
        list($headAssets, $bodyAssets) = $this->collectJavaScripts(RenderMode::PRE_COMPILE, $page, $layout, $defaultFilters);

        foreach (
            [
                'js:head:' . $page->id => $headAssets,
                'js:body:' . $page->id => $bodyAssets,
            ] as $key => $assets
        ) {
            /** @var AssetCollectionInterface $assets */

            $organizeEvent = new OrganizePreCompiledAssetsEvent(
                RenderMode::PRE_COMPILE,
                $page,
                $layout,
                $assets,
                $defaultFilters
            );
            $eventDispatcher->dispatch(
                ThemePlusEvents::ORGANIZE_PRE_COMPILED_JAVASCRIPT_ASSETS,
                $organizeEvent
            );

            $collections = $organizeEvent->getCollections();

            $generateCacheEvent = new GeneratePreCompiledAssetsCacheEvent(
                RenderMode::PRE_COMPILE,
                $page,
                $layout,
                $collections,
                $defaultFilters
            );
            $eventDispatcher->dispatch(
                ThemePlusEvents::GENERATE_PRE_COMPILED_JAVASCRIPT_ASSETS_CACHE,
                $generateCacheEvent
            );

            if (!$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']) {
                $cache->save($key, $generateCacheEvent->getCacheCode());
            }
        }
    }

    /**
     * Load all javascripts from the cache.
     *
     * @param \LayoutModel $layout
     * @param array        $scripts The search and replace array.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    protected function loadJavaScripts(\PageModel $page, \LayoutModel $layout, &$headScripts, &$bodyScripts)
    {
        global $container;

        /** @var Cache $cache */
        $cache = $container['theme-plus-assets-cache'];

        $key        = 'js:head:' . $page->id;
        $headAssets = $cache->fetch($key);

        $key        = 'js:body:' . $page->id;
        $bodyAssets = $cache->fetch($key);

        if ($headAssets && $bodyAssets) {
            /** @var FilterRulesCompiler $compiler */
            $compiler  = $container['theme-plus-filter-rules-compiler'];
            $variables = $compiler->getVariables();

            extract($variables);

            $headScripts .= eval($headAssets);
            $bodyScripts .= eval($bodyAssets);
        } else {
            $this->parseJavaScripts(RenderMode::LIVE, $page, $layout, $headScripts, $bodyScripts);
        }
    }

    /**
     * Parse all javascripts and add them to the search and replace array.
     *
     * @param \LayoutModel $layout
     * @param array        $scripts The search and replace array.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function parseJavaScripts($renderMode, \PageModel $page, \LayoutModel $layout, &$headScripts, &$bodyScripts)
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $defaultFilters = $this->getDefaultFilters($renderMode, $layout);
        list($headAssets, $bodyAssets) = $this->collectJavaScripts($renderMode, $page, $layout, $defaultFilters);

        $assetCount = count($headAssets) + count($bodyAssets);

        // inject async.js if required
        if ($layout->theme_plus_javascript_lazy_load && $assetCount) {
            if ($assetCount > 1) {
                $asyncScript = 'async_multi';
            } else {
                $asyncScript = 'async_single';
            }

            if (RenderMode::DESIGN == $renderMode) {
                $asyncScript .= '_dev';
            }

            $asset = new ExtendedFileAsset(
                TL_ROOT . '/assets/theme-plus/javascripts/' . $asyncScript . '.js',
                [],
                TL_ROOT,
                'assets/theme-plus/javascripts/' . $asyncScript . '.js'
            );
            $asset->setInline(true);

            $event = new RenderAssetHtmlEvent($renderMode, $page, $layout, $asset, $defaultFilters);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            if ($layout->theme_plus_default_javascript_position == 'body') {
                $bodyScripts .= $event->getHtml();
            } else {
                $headScripts .= $event->getHtml();
            }
        }

        // write assets html
        foreach ($headAssets as $asset) {
            $event = new RenderAssetHtmlEvent($renderMode, $page, $layout, $asset, $defaultFilters);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            $headScripts .= $event->getHtml();
        }

        foreach ($bodyAssets as $asset) {
            $event = new RenderAssetHtmlEvent($renderMode, $page, $layout, $asset, $defaultFilters);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            $bodyScripts .= $event->getHtml();
        }
    }

    /**
     * Create the default javascript filters based on the layout settings.
     *
     * @param \LayoutModel $layout
     *
     * @return \Assetic\Filter\FilterCollection|null
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getDefaultFilters($renderMode, \LayoutModel $layout)
    {
        /** @var AsseticFactory $asseticFactory */
        $asseticFactory = $GLOBALS['container']['assetic.factory'];

        // default filter
        $filters = $asseticFactory->createFilterOrChain(
            $layout->asseticJavaScriptFilter,
            RenderMode::DESIGN == $renderMode
        );

        // remove css rewrite filter
        foreach ($filters as $index => $filter) {
            if ($filter instanceof CssRewriteFilter) {
                unset($filters[$index]);
            }
        }

        return $filters;
    }

    /**
     * Parse all javascripts and add them to the search and replace array.
     *
     * @param \LayoutModel $layout
     * @param array        $scripts The search and replace array.
     *
     * @return array|AssetCollectionInterface[]
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function collectJavaScripts($renderMode, \PageModel $page, \LayoutModel $layout, FilterInterface $defaultFilters)
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        // collect head javascript assets
        $event = new CollectAssetsEvent($renderMode, $page, $layout, $defaultFilters);
        $eventDispatcher->dispatch(ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($renderMode, $page, $layout, $collection, $defaultFilters);
        $eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $headAssets = $collection;

        // collect body javascript assets
        $event = new CollectAssetsEvent($renderMode, $page, $layout, $defaultFilters);
        $eventDispatcher->dispatch(ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($renderMode, $page, $layout, $collection, $defaultFilters);
        $eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $bodyAssets = $collection;

        return [$headAssets, $bodyAssets];
    }
}
