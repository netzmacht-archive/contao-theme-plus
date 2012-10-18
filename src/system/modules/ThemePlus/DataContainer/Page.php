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

namespace InfinitySoft\ThemePlus\DataContainer;

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
                    $label = $collection->url;
                    break;

                case 'file':
                    $file = \FilesModel::findByPk($collection->file);

                    if ($file) {
                        $label = $file->path;
                        break;
                    }

                default:
                    $label = '?';
            }

            if (strlen($collection->cc)) {
                $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">[' . $collection->cc . ']</span>';
            }

            if (strlen($collection->filter)) {
                $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">' . (($collection->filterInvert)
                    ? '!'
                    : '') . '[' . implode(',',
                                          deserialize($collection->filterRule,
                                                      true)) . ']</span>';
            }

            $image = 'system/modules/ThemePlus/assets/images/' . $collection->type . '.png';

            $options[$theme->name][$collection->id] = ($image
                ? $this->generateImage($image,
                                       $label,
                                       'style="vertical-align:-3px"') . ' '
                : '') . $label;
        }

        return $options;
    }
}