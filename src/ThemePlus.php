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

use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use Doctrine\Common\Cache\Cache;

/**
 * Class ThemePlus
 */
class ThemePlus
{
    const CACHE_CREATION_TIME = 'meta:cache:creation-time';

    const CACHE_LATEST_ASSET_TIMESTAMP = 'meta:cache:latest-asset-timestamp';

    /**
     * Disable the page caching, if in pre-compile mode.
     *
     * @see \Contao\Controller::replaceDynamicScriptTags
     *
     * @param string $buffer
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function disablePageCache($buffer)
    {
        global $objPage;

        if ($objPage) {
            /** @var RenderModeDeterminer $renderModeDeterminer */
            $renderModeDeterminer = $GLOBALS['container']['theme-plus-render-mode-determiner'];

            $renderMode = $renderModeDeterminer->determineMode();

            if (RenderMode::PRE_COMPILE == $renderMode) {
                // prevent caching of the page
                $objPage->cache = false;
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function replaceCachedAssetInsertTag($tag)
    {
        if ('theme_plus_cached_asset::' === substr($tag, 0, 25)) {
            /** @var string $cacheKey The cache key. */
            $cacheKey = substr($tag, 25);

            /** @var Cache $cache The assets cache. */
            $cache = $GLOBALS['container']['theme-plus-assets-cache'];

            /** @var string $assets */
            $assets = $cache->fetch($cacheKey);

            if ($assets) {
                /** @var FilterRulesCompiler $compiler */
                $compiler = $GLOBALS['container']['theme-plus-filter-rules-compiler'];
                $variables = $compiler->getVariables();

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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function injectDeveloperTools($buffer)
    {
        global $objPage;

        if ($objPage) {
            /** @var RenderModeDeterminer $renderModeDeterminer */
            $renderModeDeterminer = $GLOBALS['container']['theme-plus-render-mode-determiner'];

            $renderMode = $renderModeDeterminer->determineMode();

            if (RenderMode::DESIGN == $renderMode) {
                /** @var DeveloperTool $developerTools */
                $developerTools = $GLOBALS['container']['theme-plus-developer-tools'];
                $buffer         = $developerTools->inject($buffer);
            }
        }

        return $buffer;
    }
}
