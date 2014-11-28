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

namespace Bit3\Contao\ThemePlus\Filter;

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

            if ($layout !== null) {
                // find the current page theme
                $theme = \ThemeModel::findByPk($layout->pid);

                if ($theme !== null) {
                    // collect all variables from theme
                    $vars = [];
                    foreach (
                        deserialize(
                            $theme->vars,
                            true
                        ) as $tmp
                    ) {
                        $vars[$tmp['key']] = $tmp['value'];
                    }

                    if (count($vars)) {
                        // Sort by key length (see #3316)
                        uksort(
                            $vars,
                            'length_sort_desc'
                        );

                        // get asset content
                        $content = $asset->getContent();

                        foreach ($vars as $key => $value) {
                            $vars[$key] = html_entity_decode($value);
                        }

                        // replace all variables
                        $content = str_replace(
                            array_keys($vars),
                            array_values($vars),
                            $content
                        );

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
