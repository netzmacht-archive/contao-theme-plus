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
 * Backend integration.
 */
class BackendIntegration
{
    /**
     * The asset cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Singleton service.
     *
     * @return BackendIntegration
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getInstance()
    {
        return $GLOBALS['container']['theme-plus-backend-integration'];
    }

    /**
     * Create a new object.
     *
     * @param Cache $cache The asset cache.
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Register custom maintenance operation and remove weekly cleanup cron.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function hookInitializeSystem()
    {
        if (!$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']) {
            $index = (1 + array_search('scripts', array_keys($GLOBALS['TL_PURGE']['folders'])));

            $GLOBALS['TL_PURGE']['folders'] = array_merge(
                array_slice($GLOBALS['TL_PURGE']['folders'], 0, $index),
                [
                    'theme_plus_aac' => [
                        'callback' => ['Bit3\Contao\ThemePlus\BackendIntegration', 'purgeAdvancedAssetCache'],
                        'affected' => []
                    ],
                ],
                array_slice($GLOBALS['TL_PURGE']['folders'], $index)
            );

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
     * Inject a backend message, if the advanced asset cache is outdated.
     *
     * @param string $content      The output buffer.
     * @param string $templateName The template name.
     *
     * @return string
     */
    public function hookOutputBackendTemplate($content, $templateName)
    {
        if (
            !$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']
            && $templateName == 'be_main'
        ) {
            $creationTime    = (int) $this->cache->fetch(ThemePlus::CACHE_CREATION_TIME);
            $latestTimestamp = (int) $this->cache->fetch(ThemePlus::CACHE_LATEST_ASSET_TIMESTAMP);

            if (!$creationTime || $latestTimestamp > $creationTime) {
                \System::loadLanguageFile('be_theme_plus');

                $template = new \TwigBackendTemplate('bit3/theme-plus/be_cache_message');

                $content = preg_replace('~</div>\s*<div.+id="container"~U', $template->parse() . "\n$0", $content, 1);
            }
        }

        return $content;
    }

    /**
     * Purge the advanced asset cache.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function purgeAdvancedAssetCache()
    {
        if ($this->cache instanceof CacheProvider) {
            $this->cache->deleteAll();
        } else {
            $_SESSION['CLEAR_CACHE_CONFIRM'] = $GLOBALS['TL_LANG']['tl_maintenance_jobs']['theme_plus_aac'][2];
            \Controller::reload();
        }
    }
}
