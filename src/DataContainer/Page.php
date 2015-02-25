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
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

namespace Bit3\Contao\ThemePlus\DataContainer;

use Bit3\Contao\ThemePlus\Model\JavaScriptModel;
use Bit3\Contao\ThemePlus\Model\StylesheetModel;

/**
 * Class Page
 */
class Page extends \Backend
{
    public function getStylesheets()
    {
        $stylesheet = StylesheetModel::findAll(['order' => 'sorting']);

        return $stylesheet
            ? $this->buildOptions($stylesheet)
            : [];
    }

    public function getJavaScripts()
    {
        $javascripts = JavaScriptModel::findAll(['order' => 'sorting']);

        return $javascripts
            ? $this->buildOptions($javascripts)
            : [];
    }

    /**
     * @param \Model\Collection $collection
     *
     * @return array
     *
     * @return array
     */
    protected function buildOptions(\Model\Collection $collection)
    {
        $options = array();

        while ($collection->next()) {
            $theme = \ThemeModel::findByPk($collection->pid);
            $label = $this->getFileLabel($collection);

            if (strlen($collection->cc)) {
                $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">[' . $collection->cc . ']</span>';
            }

            $filterRules = File::renderFilterRules($collection->row());
            if ($filterRules) {
                $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">' . $filterRules . '</span>';
            }

            $image = 'assets/theme-plus/images/' . $collection->type . '.png';

            $options[$theme->name][$collection->id] = ($image
                    ? $this->generateImage(
                        $image,
                        $label,
                        'style="vertical-align:-3px"'
                    ) . ' '
                    : '') . $label;
        }

        return $options;
    }

    /**
     * @param \Model\Collection $collection
     *
     * @return mixed|null|string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getFileLabel(\Model\Collection $collection)
    {
        switch ($collection->type) {
            case 'code':
                $label = $collection->code_snippet_title;
                break;

            case 'url':
                $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $collection->url);
                break;

            case 'file':
                if ($collection->filesource == $GLOBALS['TL_CONFIG']['uploadPath']
                    && version_compare(
                        VERSION,
                        '3',
                        '>='
                    )
                ) {
                    $file = (version_compare(VERSION, '3.2', '>=') ? \FilesModel::findByUuid($collection->file)
                        : \FilesModel::findByPk($collection->file));

                    if ($file) {
                        $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $file->path);
                        break;
                    }
                } else {
                    $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $collection->file);
                    break;
                }
            // no break

            default:
                $label = '?';

                return $label;
        }

        return $label;
    }
}
