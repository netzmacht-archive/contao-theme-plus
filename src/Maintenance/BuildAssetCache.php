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

namespace Bit3\Contao\ThemePlus\Maintenance;

use Bit3\Contao\ThemePlus\ThemePlus;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Class BuildAssetCache
 */
class BuildAssetCache implements \executable
{
    const SESSION_LAST_USERNAME = 'theme-plus.maintenance.last-username';

    public function run()
    {
        \System::loadLanguageFile('be_theme_plus');

        $GLOBALS['TL_CSS']['theme_plus_be'] = 'assets/theme-plus/stylesheets/be.css||static';

        /** @var Cache $cache */
        $cache = $GLOBALS['container']['theme-plus-assets-cache'];

        $session = \Session::getInstance();

        $template                   = new \TwigBackendTemplate('bit3/theme-plus/be_maintenance_build_asset_cache');
        $template->isActive         = $this->isActive();
        $template->action           = ampersand(\Environment::get('request'));
        $template->frontendUsername = $session->get(self::SESSION_LAST_USERNAME);

        if ($this->isBuildActive()) {
            $this->buildCache($template, $cache, $session);
        } elseif ($this->isAnalyseActive()) {
            $this->analyseCache($template, $cache);
        }

        return $template->parse();
    }

    protected function buildCache(\TwigBackendTemplate $template, Cache $cache, \Session $session)
    {
        // clear the existing caches
        /** @var \Automator $automator */
        $automator = \System::importStatic('Automator');
        $automator->purgeScriptCache();

        if ($cache instanceof CacheProvider) {
            $cache->deleteAll();
        }

        // overwrite frontend username
        $template->frontendUsername = \Input::post('user');
        $session->set(self::SESSION_LAST_USERNAME, $template->frontendUsername);

        // Use searchable pages to generate assets
        // TODO this seems to be not a good idea...
        // $GLOBALS['TL_CONFIG']['indexProtected'] = true;
        // $template->urls = \Backend::findSearchablePages(0, '', true);

        list($guestUrls, $memberUrls) = $this->buildPageUrls(0, \Environment::get('base') . '/');

        $template->guestUrls  = $guestUrls;
        $template->memberUrls = $memberUrls;

        $cache->save(ThemePlus::CACHE_CREATION_TIME, time());
    }

    protected function analyseCache(\TwigBackendTemplate $template, Cache $cache)
    {
        $GLOBALS['TL_CSS']['mediabox'] = sprintf(
            'assets/mootools/mediabox/%s/css/mediaboxAdvWhite21.css||static',
            MEDIABOX
        );

        $root = new \stdClass();
        $root->id = 0;
        $root->children = new \ArrayObject();

        $this->buildPageTree($root, $cache);

        $template->pages = $root->children;
        $template->mediaboxVersion = MEDIABOX;
    }

    protected function buildPageUrls($pid, $baseUrl, array &$guestUrls = [], array &$memberUrls = [])
    {
        $pageModels = \PageModel::findBy(
            ['pid=? AND (start=? OR start<?) AND (stop=? OR stop>?) AND published=?'],
            [$pid, '', time(), '', time(), 1],
            ['order' => 'sorting']
        );

        if ($pageModels) {
            foreach ($pageModels as $pageModel) {
                if (in_array($pageModel->type, ['root'])) {
                    $baseUrl =
                        ($pageModel->useSSL ? 'https://' : 'http://')
                        . ($pageModel->dns ?: \Environment::get('httpHost'))
                        . \Environment::get('path')
                        . '/';
                } elseif (
                    !$pageModel->theme_plus_disable_assets_cache
                    && !in_array($pageModel->type, ['forward', 'redirect'])
                ) {
                    $url = $baseUrl . \Controller::generateFrontendUrl($pageModel->row());

                    if ($pageModel->protected) {
                        $memberUrls[] = $url;
                    } else {
                        $guestUrls[] = $url;
                    }
                }

                $this->buildPageUrls($pageModel->id, $baseUrl, $guestUrls, $memberUrls);
            }
        }

        return [$guestUrls, $memberUrls];
    }

    protected function buildPageTree(\stdClass $parent, Cache $cache)
    {
        $pageModels = \PageModel::findBy(
            ['pid=? AND (start=? OR start<?) AND (stop=? OR stop>?) AND published=?'],
            [$parent->id, '', time(), '', time(), 1],
            ['order' => 'sorting']
        );

        if ($pageModels) {
            foreach ($pageModels as $pageModel) {
                $pageObject = new \stdClass();
                $pageObject->id             = $pageModel->id;
                $pageObject->title          = $pageModel->title;
                $pageObject->children       = new \ArrayObject();

                if (
                    $pageModel->theme_plus_disable_assets_cache
                    || in_array($pageModel->type, ['root', 'forward', 'redirect'])
                ) {
                    $pageObject->hasCache = false;
                } else {
                    $cachedCss    = ($raw = $cache->fetch('css:' . $pageModel->id))
                        ? highlight_string('<?php' . $raw, true)
                        : false;
                    $cachedHeadJs = ($raw = $cache->fetch('js:head:' . $pageModel->id))
                        ? highlight_string('<?php' . $raw, true)
                        : false;
                    $cachedBodyJs = ($raw = $cache->fetch('js:body:' . $pageModel->id))
                        ? highlight_string('<?php' . $raw, true)
                        : false;

                    $pageObject->hasCache    = true;
                    $pageObject->cssCache    = $cachedCss;
                    $pageObject->headJsCache = $cachedHeadJs;
                    $pageObject->bodyJsCache = $cachedBodyJs;
                }

                $parent->children->append($pageObject);

                $this->buildPageTree($pageObject, $cache);
            }
        }
    }

    public function isBuildActive()
    {
        return \Input::post('tl_theme_plus_build_cache') != '';
    }

    public function isAnalyseActive()
    {
        return \Input::post('tl_theme_plus_analyse_cache') != '';
    }

    public function isActive()
    {
        return \Input::get('highlight') == 'theme_plus_build_assets_cache'
            || $this->isBuildActive()
            || $this->isAnalyseActive();
    }
}
