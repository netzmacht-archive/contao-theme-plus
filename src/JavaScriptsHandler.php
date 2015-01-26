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
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\FilterInterface;
use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Event\GeneratePreCompiledAssetsCacheEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Bit3\Contao\ThemePlus\Event\OrganizePreCompiledAssetsEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use DependencyInjection\Container\PageProvider;
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
     * @var PageProvider
     */
    private $pageProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var AsseticFactory
     */
    private $asseticFactory;

    /**
     * @var RenderModeDeterminer
     */
    private $renderModeDeterminer;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var FilterRulesCompiler
     */
    private $compiler;

    /**
     * Singleton service.
     *
     * @return JavaScriptsHandler
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getInstance()
    {
        return $GLOBALS['container']['theme-plus-javascript-handler'];
    }

    public function __construct(
        PageProvider $pageProvider,
        EventDispatcherInterface $eventDispatcher,
        AsseticFactory $asseticFactory,
        RenderModeDeterminer $renderModeDeterminer,
        Cache $cache,
        FilterRulesCompiler $compiler
    ) {
        $this->pageProvider         = $pageProvider;
        $this->eventDispatcher      = $eventDispatcher;
        $this->asseticFactory       = $asseticFactory;
        $this->renderModeDeterminer = $renderModeDeterminer;
        $this->cache                = $cache;
        $this->compiler             = $compiler;
    }

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
     */
    public function hookReplaceDynamicScriptTags($buffer)
    {
        $page = $this->pageProvider->getPage();

        if ($page) {
            $headScripts = '';
            $bodyScripts = '';

            // search for the layout
            $layout = \LayoutModel::findByPk($page->layout);

            $renderMode = $this->renderModeDeterminer->determineMode();

            if (RenderMode::PRE_COMPILE == $renderMode) {
                // pre-compile javascripts
                $this->compileJavaScripts(
                    $page,
                    $layout
                );
            } elseif (
                !$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']
                && RenderMode::LIVE == $renderMode
            ) {
                // load cached javascripts
                $this->loadJavaScripts(
                    $page,
                    $layout,
                    $headScripts,
                    $bodyScripts
                );
            } else {
                // dynamically parse javascripts
                $this->parseJavaScripts(
                    $renderMode,
                    $page,
                    $layout,
                    $headScripts,
                    $bodyScripts
                );
            }

            $GLOBALS['TL_JAVASCRIPT'] = [];

            // replace dynamic scripts
            return str_replace(
                ['[[TL_HEAD]]', '[[TL_SCRIPTS]]'],
                [$headScripts . '[[TL_HEAD]]', $bodyScripts],
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
     */
    protected function compileJavaScripts(\PageModel $page, \LayoutModel $layout)
    {
        $defaultFilters = $this->getDefaultFilters(RenderMode::PRE_COMPILE, $layout);

        /** @var AssetCollectionInterface $headAssets */
        /** @var AssetCollectionInterface $bodyAssets */
        list($headAssets, $bodyAssets) =
            $this->collectJavaScripts(RenderMode::PRE_COMPILE, $page, $layout, $defaultFilters);

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
            $this->eventDispatcher->dispatch(
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
            $this->eventDispatcher->dispatch(
                ThemePlusEvents::GENERATE_PRE_COMPILED_JAVASCRIPT_ASSETS_CACHE,
                $generateCacheEvent
            );

            if (!$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']) {
                $this->cache->save($key, $generateCacheEvent->getCacheCode());
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
        $headKey    = 'js:head:' . $page->id;
        $headAssets = $this->cache->fetch($headKey);

        $bodyKey    = 'js:body:' . $page->id;
        $bodyAssets = $this->cache->fetch($bodyKey);

        if ($headAssets && $bodyAssets) {
            if ($page->cache) {
                $headScripts .= sprintf('{{theme_plus_cached_asset::%s|uncached}}', $headKey);
                $bodyScripts .= sprintf('{{theme_plus_cached_asset::%s|uncached}}', $bodyKey);
            } else {
                $variables = $this->compiler->getVariables();

                extract($variables);

                $headScripts .= eval($headAssets);
                $bodyScripts .= eval($bodyAssets);
            }
        } else {
            $page->cache = false;
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
     */
    protected function parseJavaScripts(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        &$headScripts,
        &$bodyScripts
    ) {
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
            $this->eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            if ($layout->theme_plus_default_javascript_position == 'body') {
                $bodyScripts .= $event->getHtml();
            } else {
                $headScripts .= $event->getHtml();
            }
        }

        // write assets html
        foreach ($headAssets as $asset) {
            $event = new RenderAssetHtmlEvent($renderMode, $page, $layout, $asset, $defaultFilters);
            $this->eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            $headScripts .= $event->getHtml();
        }

        foreach ($bodyAssets as $asset) {
            $event = new RenderAssetHtmlEvent($renderMode, $page, $layout, $asset, $defaultFilters);
            $this->eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            $bodyScripts .= $event->getHtml();
        }
    }

    /**
     * Create the default javascript filters based on the layout settings.
     *
     * @param \LayoutModel $layout
     *
     * @return \Assetic\Filter\FilterCollection|null
     */
    protected function getDefaultFilters($renderMode, \LayoutModel $layout)
    {
        // default filter
        $filters = $this->asseticFactory->createFilterOrChain(
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
     */
    protected function collectJavaScripts(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        FilterInterface $defaultFilters
    ) {
        // collect head javascript assets
        $event = new CollectAssetsEvent($renderMode, $page, $layout, $defaultFilters);
        $this->eventDispatcher->dispatch(ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($renderMode, $page, $layout, $collection, $defaultFilters);
        $this->eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $headAssets = $collection;

        // collect body javascript assets
        $event = new CollectAssetsEvent($renderMode, $page, $layout, $defaultFilters);
        $this->eventDispatcher->dispatch(ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($renderMode, $page, $layout, $collection, $defaultFilters);
        $this->eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $bodyAssets = $collection;

        return [$headAssets, $bodyAssets];
    }
}
