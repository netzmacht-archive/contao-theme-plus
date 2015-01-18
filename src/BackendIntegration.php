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

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Class BackendIntegration
 */
class BackendIntegration
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function hookInitializeSystem()
    {
        if (!$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']) {

            foreach ($GLOBALS['TL_CRON']['weekly'] as $index => $callback) {
                if (
                    'Automator' == $callback[0]
                    && 'purgeScriptCache' == $callback[1]
                ) {
                    unset($GLOBALS['TL_CRON']['weekly'][$index]);
                }
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function hookOutputBackendTemplate($content, $templateName)
    {
        if (
            !$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']
            && $templateName == 'be_main'
        ) {
            /** @var Cache $cache */
            $cache = $GLOBALS['container']['theme-plus-assets-cache'];

            $creationTime    = (int) $cache->fetch(ThemePlus::CACHE_CREATION_TIME);
            $latestTimestamp = (int) $cache->fetch(ThemePlus::CACHE_LATEST_ASSET_TIMESTAMP);

            if (!$creationTime || $latestTimestamp > $creationTime) {
                \System::loadLanguageFile('be_theme_plus');

                $template = new \TwigBackendTemplate('bit3/theme-plus/be_cache_message');

                $content = preg_replace('~</div>\s*<div.+id="container"~U', $template->parse() . "\n$0", $content, 1);
            }
        }

        return $content;
    }
}
