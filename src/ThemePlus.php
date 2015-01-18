<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @author     Stefan heimes <stefan_heimes@hotmail.com>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

namespace Bit3\Contao\ThemePlus;

use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use DependencyInjection\Container\PageProvider;
use Doctrine\Common\Cache\Cache;

/**
 * Class ThemePlus
 */
class ThemePlus
{
    const CACHE_CREATION_TIME = 'meta:cache:creation-time';

    const CACHE_LATEST_ASSET_TIMESTAMP = 'meta:cache:latest-asset-timestamp';

    /**
     * @var PageProvider
     */
    private $pageProvider;

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
     * @var DeveloperTool
     */
    private $developerTool;

    /**
     * Singleton service.
     *
     * @return ThemePlus
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getInstance()
    {
        return $GLOBALS['container']['theme-plus-generic-handler'];
    }

    public function __construct(
        PageProvider $pageProvider,
        RenderModeDeterminer $renderModeDeterminer,
        Cache $cache,
        FilterRulesCompiler $compiler,
        DeveloperTool $developerTool
    ) {
        $this->pageProvider         = $pageProvider;
        $this->renderModeDeterminer = $renderModeDeterminer;
        $this->cache                = $cache;
        $this->compiler             = $compiler;
        $this->developerTool        = $developerTool;
    }

    /**
     * Disable the page caching, if in pre-compile mode.
     *
     * @see \Contao\Controller::replaceDynamicScriptTags
     *
     * @param string $buffer
     *
     * @return string
     */
    public function disablePageCache($buffer)
    {
        $page = $this->pageProvider->getPage();

        if ($page) {
            $renderMode = $this->renderModeDeterminer->determineMode();

            if (RenderMode::PRE_COMPILE == $renderMode) {
                // prevent caching of the page
                $page->cache = false;
            }
        }

        return $buffer;
    }

    /**
     * Replace the {{theme_plus_cached_asset::*}} insert tag.
     *
     * @see \Contao\Controller::replaceInsertTags
     *
     * @param $tag
     *
     * @return bool|mixed|string
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function replaceCachedAssetInsertTag($tag)
    {
        if ('theme_plus_cached_asset::' === substr($tag, 0, 25)) {
            /** @var string $cacheKey The cache key. */
            $cacheKey = substr($tag, 25);

            /** @var string $assets */
            $assets = $this->cache->fetch($cacheKey);

            if ($assets) {
                $variables = $this->compiler->getVariables();

                extract($variables);

                return eval($assets);
            }

            return '';
        }

        return false;
    }

    /**
     * Inject the developer tools in designer mode.
     *
     * @see \Contao\Controller::replaceDynamicScriptTags
     *
     * @param string $buffer
     *
     * @return string
     */
    public function injectDeveloperTools($buffer)
    {
        $page = $this->pageProvider->getPage();

        if ($page) {
            $renderMode = $this->renderModeDeterminer->determineMode();

            if (RenderMode::DESIGN == $renderMode) {
                $buffer = $this->developerTool->inject($buffer);
            }
        }

        return $buffer;
    }
}
