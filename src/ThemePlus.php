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

use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use FrontendTemplate;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Template;

/**
 * Class ThemePlus
 *
 * Adding files to the page layout.
 */
class ThemePlus
{
    /**
     * @var DeveloperTool
     */
    protected $developerTool;

    /**
     * @see \Contao\Template::parse
     *
     * @param \Template $template
     */
    public function hookParseTemplate(Template $template)
    {
        if ($template instanceof FrontendTemplate) {
            if (substr($template->getName(), 0, 3) == 'fe_') {
                $template->mootools = '[[TL_THEME_PLUS]]' . "\n" . $template->mootools;
            }
        }
    }

    /**
     * @see \Contao\Controller::replaceDynamicScriptTags
     *
     * @param $buffer
     */
    public function hookReplaceDynamicScriptTags($buffer)
    {
        global $objPage;

        if ($objPage) {
            if (ThemePlusEnvironment::isDesignerMode()) {
                $this->developerTool = new DeveloperTool();
            }

            // the search and replace array
            $sr = [
                '[[TL_CSS]]'        => '',
                '[[TL_THEME_PLUS]]' => '',
                '[[TL_HEAD]]'       => '',
            ];

            // search for the layout
            $layout = \LayoutModel::findByPk($objPage->layout);

            // parse stylesheets
            $this->parseStylesheets(
                $layout,
                $sr
            );

            // parse javascripts
            $this->parseJavaScripts(
                $layout,
                $sr
            );

            /*
            $this->excludeList = [];

            // build exclude list
            if (is_array($GLOBALS['TL_THEME_EXCLUDE'])) {
                $this->excludeList = array_merge($this->excludeList, $GLOBALS['TL_THEME_EXCLUDE']);
            }
            if (!is_array($layout->theme_plus_exclude_files)) {
                $layout->theme_plus_exclude_files = deserialize(
                    $layout->theme_plus_exclude_files,
                    true
                );
            }
            if (count($layout->theme_plus_exclude_files) > 0) {
                foreach ($layout->theme_plus_exclude_files as $v) {
                    if ($v[0]) {
                        $this->excludeList[] = $v[0];
                    }
                }
            }
            */

            if (ThemePlusEnvironment::isDesignerMode()) {
                $buffer = $this->developerTool->inject($buffer);
            }

            // replace dynamic scripts
            return str_replace(
                array_keys($sr),
                array_values($sr),
                $buffer
            );
        }

        return $buffer;
    }

    /**
     * Parse all stylesheets and add them to the search and replace array.
     *
     * @param \LayoutModel $layout
     * @param array        $sr The search and replace array.
     *
     * @return mixed
     */
    protected function parseStylesheets(\LayoutModel $layout, array &$sr)
    {
        global $objPage;

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        // collect stylesheet assets
        $event = new CollectAssetsEvent($objPage, $layout);
        $eventDispatcher->dispatch(ThemePlusEvents::COLLECT_STYLESHEET_ASSETS, $event);

        $collection = $event->getAssets();

        /** @var AsseticFactory $asseticFactory */
        $asseticFactory = $GLOBALS['container']['assetic.factory'];

        // default filter
        $defaultFilters = $asseticFactory->createFilterOrChain(
            $layout->asseticStylesheetFilter,
            ThemePlusEnvironment::isDesignerMode()
        );

        $event = new OrganizeAssetsEvent($objPage, $layout, $defaultFilters, $collection, $this->developerTool);
        $eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_STYLESHEET_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $assets     = $collection->all();

        foreach ($assets as $asset) {
            $event = new RenderAssetHtmlEvent($objPage, $layout, $defaultFilters, $asset, $this->developerTool);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_STYLESHEET_HTML, $event);

            $sr['[[TL_CSS]]'] .= $event->getHtml();
        }
    }

    /**
     * Parse all javascripts and add them to the search and replace array.
     *
     * @param \LayoutModel $layout
     * @param array        $sr The search and replace array.
     *
     * @return mixed
     */
    protected function parseJavaScripts(\LayoutModel $layout, array &$sr)
    {
        global $objPage;

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        if (!ThemePlusEnvironment::isDesignerMode()) {
            /** @var AsseticFactory $asseticFactory */
            $asseticFactory = $GLOBALS['container']['assetic.factory'];

            // default filter
            $defaultFilters = $asseticFactory->createFilterOrChain(
                $layout->asseticJavaScriptFilter,
                ThemePlusEnvironment::isDesignerMode()
            );
        } else {
            $defaultFilters = null;
        }

        // collect head javascript assets
        $event = new CollectAssetsEvent($objPage, $layout);
        $eventDispatcher->dispatch(ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($objPage, $layout, $defaultFilters, $collection, $this->developerTool);
        $eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $headAssets = $collection->all();

        // collect body javascript assets
        $event = new CollectAssetsEvent($objPage, $layout);
        $eventDispatcher->dispatch(ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getAssets();

        $event = new OrganizeAssetsEvent($objPage, $layout, $defaultFilters, $collection, $this->developerTool);
        $eventDispatcher->dispatch(ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS, $event);

        $collection = $event->getOrganizedAssets();
        $bodyAssets = $collection->all();

        $assetCount = count($headAssets) + count($bodyAssets);

        // inject async.js if required
        if ($layout->theme_plus_javascript_lazy_load && $assetCount) {
            if ($assetCount > 1) {
                $asyncScript = 'async_multi';
            } else {
                $asyncScript = 'async_single';
            }

            if ($this->developerTool) {
                $asyncScript .= '_dev';
            }

            $asset = new ExtendedFileAsset(
                TL_ROOT . '/assets/theme-plus/javascripts/' . $asyncScript . '.js',
                [],
                TL_ROOT,
                'assets/theme-plus/javascripts/' . $asyncScript . '.js'
            );
            $asset->setInline(true);

            $event = new RenderAssetHtmlEvent($objPage, $layout, $defaultFilters, $asset, $this->developerTool);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            if ($layout->theme_plus_default_javascript_position == 'body') {
                $sr['[[TL_THEME_PLUS]]'] .= $event->getHtml();
            } else {
                $sr['[[TL_HEAD]]'] .= $event->getHtml();
            }
        }

        // write assets html
        foreach ($headAssets as $asset) {
            $event = new RenderAssetHtmlEvent($objPage, $layout, $defaultFilters, $asset, $this->developerTool);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            $sr['[[TL_HEAD]]'] .= $event->getHtml();
        }

        foreach ($bodyAssets as $asset) {
            $event = new RenderAssetHtmlEvent($objPage, $layout, $defaultFilters, $asset, $this->developerTool);
            $eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

            $sr['[[TL_THEME_PLUS]]'] .= $event->getHtml();
        }
    }
}
