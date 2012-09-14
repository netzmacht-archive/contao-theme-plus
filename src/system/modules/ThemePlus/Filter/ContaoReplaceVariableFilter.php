<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2012 InfinitySoft <http://www.infinitysoft.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace InfinitySoft\ThemePlus\Filter;

/**
 * Class PageRegular
 */
class ContaoReplaceVariableFilter
    implements \Assetic\Filter\FilterInterface
{
    /**
     * Filters an asset after it has been loaded.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterLoad(\Assetic\Asset\AssetInterface $asset)
    {
        if ($GLOBALS['objPage']) {
            // find the current page layout
            $layout = \LayoutModel::findByPk($GLOBALS['objPage']->layout);

            if ($layout !== null && $layout->next()) {
                // find the current page theme
                $theme = \ThemeModel::findByPk($layout->pid);

                if ($theme !== null && $theme->next()) {
                    // collect all variables from theme
                    $vars = array();
                    foreach (deserialize($theme->vars,
                                         true) as $tmp) {
                        $vars[$tmp['key']] = $tmp['value'];
                    }

                    if (count($vars)) {
                        // Sort by key length (see #3316)
                        uksort($vars,
                               'length_sort_desc');

                        // get asset content
                        $content = $asset->getContent();

                        // replace all variables
                        $content = str_replace(array_keys($vars),
                                               array_values($vars),
                                               $content);

                        // set asset content
                        $asset->setContent($content);
                    }
                }
            }
        }
    }

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterDump(\Assetic\Asset\AssetInterface $asset)
    {
    }
}
