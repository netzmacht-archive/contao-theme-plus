<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Bit3\Contao\ThemePlus\Renderer;


use Assetic\Asset\AssetInterface;
use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use DependencyInjection\Container\PageProvider;

abstract class AbstractRendererSubscriber
{
    /**
     * The page provider.
     *
     * @var PageProvider
     */
    protected $pageProvider;

    /**
     * The developer tool.
     *
     * @var DeveloperTool
     */
    protected $developerTool;

    /**
     * Construct.
     *
     * @param PageProvider  $pageProvider  The page provider.
     * @param DeveloperTool $developerTool The developer tool.
     */
    public function __construct(PageProvider $pageProvider, DeveloperTool $developerTool)
    {
        $this->pageProvider  = $pageProvider;
        $this->developerTool = $developerTool;
    }

    /**
     * Store asset in session an return the session id.
     *
     * @param AssetInterface $asset The asset.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function storeInSession(AssetInterface $asset)
    {
        // get the session object
        $sessionId = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);
        $session   = unserialize($_SESSION['THEME_PLUS_ASSETS'][$sessionId]);

        if (!$session || $asset->getLastModified() > $session->asset->getLastModified()) {
            $session        = new \stdClass;
            $session->page  = $this->pageProvider->getPage()->id;
            $session->asset = $asset;

            $_SESSION['THEME_PLUS_ASSETS'][$sessionId] = serialize($session);
        }

        return $sessionId;
    }

    /**
     * Get target path.
     *
     * @return string
     */
    protected function getTargetPath()
    {
        // retrieve page path
        $targetPath = \Environment::get('requestUri');
        // remove query string
        $targetPath = preg_replace('~\?\.*~', '', $targetPath);
        // remove leading /
        $targetPath = ltrim($targetPath, '/');

        return $targetPath;
    }

    /**
     * Get real assets from assets wrapped by a delegator asset.
     *
     * @param $asset The asset.
     *
     * @return AssetInterface
     */
    protected function getRealAssets($asset)
    {
        $realAssets = $asset;
        while ($realAssets instanceof DelegatorAssetInterface) {
            $realAssets = $realAssets->getAsset();
        }

        return $realAssets;
    }

    /**
     * @param $type
     * @param $realAssets
     * @param $sessionId
     *
     * @return mixed|string
     */
    protected function getDesignerModeProxyUrl($type, $realAssets, $sessionId)
    {
        if ($realAssets instanceof FileAsset) {
            $name = basename($realAssets->getSourcePath());
        } else {
            if ($realAssets instanceof HttpAsset) {
                $class    = new \ReflectionClass('Assetic\Asset\HttpAsset');
                $property = $class->getProperty('sourceUrl');
                $property->setAccessible(true);
                $url  = $property->getValue($realAssets);
                $name = 'url_' . basename(parse_url($url, PHP_URL_PATH));
            } else {
                $name = 'asset_' . $sessionId;
            }
        }

        // generate the proxy url
        $url = sprintf(
            'assets/theme-plus/proxy.php/%s/%s/%s',
            $type,
            $sessionId,
            $name
        );

        return $url;
    }
}
