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

/**
 * Class ThemePlus
 */
class ThemePlus
{
    const CACHE_CREATION_TIME = 'meta:cache:creation-time';

    const CACHE_LATEST_ASSET_TIMESTAMP = 'meta:cache:latest-asset-timestamp';

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
            /** @var RenderModeDeterminer $renderModeDeterminer */
            $renderModeDeterminer = $GLOBALS['container']['theme-plus-render-mode-determiner'];

            $renderMode = $renderModeDeterminer->determineMode();

            if (RenderMode::PRE_COMPILE == $renderMode) {
                // prevent caching of the page
                $objPage->cache = false;
            } elseif (RenderMode::DESIGN == $renderMode) {
                /** @var DeveloperTool $developerTools */
                $developerTools = $GLOBALS['container']['theme-plus-developer-tools'];
                $buffer         = $developerTools->inject($buffer);
            }
        }

        return $buffer;
    }
}
