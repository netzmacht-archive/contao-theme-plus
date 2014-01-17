<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\ThemePlus\DataContainer;

use \ThemePlus\Model\StylesheetModel;
use \ThemePlus\Model\JavaScriptModel;

/**
 * Class Page
 */
class Page
    extends \Backend
{
    public function getStylesheets($dc)
    {
        $stylesheet = StylesheetModel::findAll(array('order' => 'sorting'));

        return $stylesheet
            ? $this->buildOptions($stylesheet)
            : array();
    }

    public function getJavaScripts($dc)
    {
        $javascripts = JavaScriptModel::findAll(array('order' => 'sorting'));

        return $javascripts
            ? $this->buildOptions($javascripts)
            : array();
    }

    protected function buildOptions(\Model\Collection $collection)
    {
        while ($collection->next()) {
            $theme = \ThemeModel::findByPk($collection->pid);

            switch ($collection->type) {
                case 'code':
                    $label = $collection->code_snippet_title;
                    break;

                case 'url':
                    $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $collection->url);
                    break;

                case 'file':
					if ($collection->filesource == $GLOBALS['TL_CONFIG']['uploadPath'] && version_compare(VERSION, '3', '>=')) {
						$file = (version_compare(VERSION, '3.2', '>=') ? \FilesModel::findByUuid($collection->file) : \FilesModel::findByPk($collection->file));

						if ($file) {
							$label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $file->path);
							break;
						}
					}
					else {
						$label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $collection->file);
						break;
					}

                default:
                    $label = '?';
            }

            if (strlen($collection->cc)) {
                $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">[' . $collection->cc . ']</span>';
            }

			$filterRules = File::renderFilterRules($collection->row());
			if ($filterRules) {
                $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">' . $filterRules . '</span>';
            }

            $image = 'system/modules/theme-plus/assets/images/' . $collection->type . '.png';

            $options[$theme->name][$collection->id] = ($image
                ? $this->generateImage($image,
                                       $label,
                                       'style="vertical-align:-3px"') . ' '
                : '') . $label;
        }

        return $options;
    }
}