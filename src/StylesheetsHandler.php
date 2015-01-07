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

use Assetic\Filter\FilterInterface;
use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Event\GeneratePreCompiledAssetsCacheEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Bit3\Contao\ThemePlus\Event\OrganizePreCompiledAssetsEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use Detection\MobileDetect;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class StylesheetsHandler
 */
class StylesheetsHandler
{
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
            // search for the layout
            $layout = \LayoutModel::findByPk($objPage->layout);

            /** @var RenderModeDeterminer $renderModeDeterminer */
            $renderModeDeterminer = $GLOBALS['container']['theme-plus-render-mode-determiner'];

            $renderMode = $renderModeDeterminer->determineMode();

            // the stylesheets buffer
            $stylesheets = '';

            if (RenderMode::PRE_COMPILE == $renderMode) {
                // pre-compile stylesheets
                $this->compileStylesheets(
                    $objPage,
                    $layout
                );
            } elseif (
                !$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']
                && RenderMode::LIVE == $renderMode
            ) {
                // load cached stylesheets
                $this->loadStylesheets(
                    $objPage,
                    $layout,
                    $stylesheets
                );
            } else {
                // dynamically parse stylesheets
                $this->parseStylesheets(
                    $renderMode,
                    $objPage,
                    $layout,
                    $stylesheets
                );
            }

            $GLOBALS['TL_FRAMEWORK_CSS'] = [];
            $GLOBALS['TL_CSS']           = [];
            $GLOBALS['TL_USER_CSS']      = [];

            // replace dynamic scripts
            return str_replace(
                '[[TL_CSS]]',
                $stylesheets,
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
    protected function compileStylesheets(\PageModel $page, \LayoutModel $layout)
    {
        /** @var Cache $cache */
        $cache = $GLOBALS['container']['theme-plus-assets-cache'];

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $defaultFilters = $this->getDefaultFilters(RenderMode::PRE_COMPILE, $layout);
        $assets         = $this->collectStylesheets(RenderMode::PRE_COMPILE, $page, $layout, $defaultFilters);

        $organizeEvent = new OrganizePreCompiledAssetsEvent(
            RenderMode::PRE_COMPILE,
            $page,
            $layout,
            $assets,
            $defaultFilters
        );
        $eventDispatcher->dispatch(
            ThemePlusEvents::ORGANIZE_PRE_COMPILED_STYLESHEET_ASSETS,
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
            ThemePlusEvents::GENERATE_PRE_COMPILED_STYLESHEET_ASSETS_CACHE,
            $generateCacheEvent
        );

        if (!$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']) {
            $cache->save('css:' . $page->id, $generateCacheEvent->getCacheCode());
        }
    }

    /**
     * Load all stylesheets from the cache.
     *
     * @param \LayoutModel $layout
     * @param array        $stylesheets The search and replace array.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    protected function loadStylesheets(\PageModel $page, \LayoutModel $layout, &$stylesheets)
    {
        global $container;

        /** @var Cache $cache */
        $cache = $container['theme-plus-assets-cache'];

        $key    = 'css:' . $page->id;
        $assets = $cache->fetch($key);

        if ($assets) {
            /** @var FilterRulesCompiler $compiler */
            $compiler = $container['theme-plus-filter-rules-compiler'];
            $variables = $compiler->getVariables();

            extract($variables);

            $stylesheets .= eval($assets);
        } else {
            $this->parseStylesheets(RenderMode::LIVE, $page, $layout, $stylesheets);
        }
    }

    /**
     * Parse all stylesheets and add them to the search and replace array.
     *
     * @param \LayoutModel $layout
     * @param string       $stylesheets The search and replace array.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function parseStylesheets($renderMode, \PageModel $page, \LayoutModel $layout, &$stylesheets)
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $defaultFilters = $this->getDefaultFilters($renderMode, $layout);
        $assets         = $this->collectStylesheets($renderMode, $page, $layout, $defaultFilters);

        foreach ($assets as $asset) {
            $event = new RenderAssetHtmlEvent($renderMode, $page, $layout, $asset, $defaultFilters);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_STYLESHEET_HTML, $event);

            $stylesheets .= $event->getHtml();
        }
    }

    /**
     * Create the default stylesheet filters based on the layout settings.
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
            $layout->asseticStylesheetFilter,
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
     * Collect stylesheets and return them as array.
     *
     * @param \LayoutModel    $layout
     * @param FilterInterface $defaultFilters
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function collectStylesheets($renderMode, \PageModel $page, \LayoutModel $layout, FilterInterface $defaultFilters)
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        // collect stylesheet assets
        $event = new CollectAssetsEvent($renderMode, $page, $layout, $defaultFilters);
        $eventDispatcher->dispatch(ThemePlusEvents::COLLECT_STYLESHEET_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($renderMode, $page, $layout, $collection, $defaultFilters);
        $eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_STYLESHEET_ASSETS, $event);

        $collection = $event->getOrganizedAssets();

        return $collection;
    }
}
