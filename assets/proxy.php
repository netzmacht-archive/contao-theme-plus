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

define('TL_MODE', 'FE');
require(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) . '/system/initialize.php');

use Assetic\Asset\AssetInterface;
use Assetic\Asset\StringAsset;
use Assetic\Filter\CssRewriteFilter;
use Doctrine\Common\Cache\Cache;
use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use Bit3\Contao\ThemePlus\RenderMode;
use Bit3\Contao\ThemePlus\RenderModeDeterminer;
use Bit3\Contao\ThemePlus\ThemePlus;

class proxy
{
    public function run()
    {
        /** @var RenderModeDeterminer $renderModeDeterminer */
        $renderModeDeterminer = $GLOBALS['container']['theme-plus-render-mode-determiner'];

        $renderMode = $renderModeDeterminer->determineMode();

        if (RenderMode::DESIGN == $renderMode) {
            $user = FrontendUser::getInstance();
            $user->authenticate();

            /** @var DeveloperTool $developerTool */
            $developerTool = $GLOBALS['container']['theme-plus-developer-tools'];

            $pathInfo = \Environment::get('pathInfo');

            list($type, $id) = explode('/', substr($pathInfo, 1));

            // output headers
            header("Cache-Control: public");
            switch ($type) {
                case 'css':
                    header('Content-Type: text/css; charset=utf-8');
                    break;

                case 'js':
                    header('Content-Type: text/javascript; charset=utf-8');
                    break;
            }

            if (isset($_SESSION['THEME_PLUS_ASSETS'][$id])) {
                $session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

                // load asset from session
                /** @var AssetInterface $asset */
                $asset = $session->asset;
                /** @var AssetInterface $cache */
                $cache = $session->cache;

                if (false && $cache && $cache->getLastModified() >= $asset->getLastModified()) {
                    header('X-Theme-Plus-Rendering: cached');
                    echo $cache->getContent();
                    ob_flush();
                    return;
                }

                header('X-Theme-Plus-Rendering: live');

                // load page from session
                $GLOBALS['objPage'] = $page = \PageModel::findWithDetails($session->page);

                /** @var AsseticFactory $asseticFactory */
                $asseticFactory = $GLOBALS['container']['assetic.factory'];

                // default filter
                $defaultFilters = $asseticFactory->createFilterOrChain($page->layout->asseticStylesheetFilter, true);

                // remove css rewrite filter
                foreach ($defaultFilters as $index => $filter) {
                    if ($filter instanceof CssRewriteFilter) {
                        unset($defaultFilters[$index]);
                    }
                }

                // update the target path
                $asset->setTargetPath('assets/theme-plus/proxy.php/:type/:id/:name');

                // create debug informations
                $buffer = '/*' . PHP_EOL;
                $buffer .= ' * DEBUG' . PHP_EOL;
                $buffer .= $developerTool->getAssetDebugString($asset, ' * ') . PHP_EOL;
                $buffer .= ' * END' . PHP_EOL;
                $buffer .= ' */' . PHP_EOL . PHP_EOL;

                // dump the asset
                $buffer .= $asset->dump($defaultFilters);

                $cachedAsset = new StringAsset($buffer, [], $asset->getSourceRoot(), $asset->getSourcePath());
                $cachedAsset->setTargetPath($asset->getTargetPath());
                $cachedAsset->setLastModified($asset->getLastModified());
                $cachedAsset->load();

                $session->cache                     = $cachedAsset;
                $_SESSION['THEME_PLUS_ASSETS'][$id] = serialize($session);

                // update advanced asset cache meta
                if (!$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching']) {
                    /** @var Cache $cache */
                    $cache = $GLOBALS['container']['theme-plus-assets-cache'];

                    $latestTimestamp = $cache->fetch(ThemePlus::CACHE_LATEST_ASSET_TIMESTAMP);

                    if (!$latestTimestamp || $latestTimestamp < $asset->getLastModified()) {
                        $cache->save(ThemePlus::CACHE_LATEST_ASSET_TIMESTAMP, $asset->getLastModified());
                    }
                }

                echo $buffer;
                ob_flush();
                return;
            }
        }

        header('HTTP/1.1 403 Forbidden');
        header('Status: 403 Forbidden');
        header('Content-Type: text/plain; charset=UTF-8');
        echo '403 Forbidden';
    }
}

$proxy = new proxy();
$proxy->run();
