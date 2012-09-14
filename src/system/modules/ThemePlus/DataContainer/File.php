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

use \Assetic\Model\FilterModel;
use \Assetic\Model\FilterChainModel;

/**
 * Class ThemePlus
 */
class File
    extends \Backend
{
    /**
     * List an file
     *
     * @param array
     *
     * @return string
     */
    public function listFile($row)
    {
        switch ($row['type']) {
            case 'code':
                $label = $row['code_snippet_title'];
                break;

            case 'url':
                $label = $row['url'];
                break;

            case 'file':
                $file = \FilesModel::findByPk($row['file']);

                if ($file) {
                    $label = $file->path;
                    break;
                }

            default:
                $label = '?';
        }

        if (strlen($row['cc'])) {
            $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">[' . $row['cc'] . ']</span>';
        }

        if (strlen($row['media'])) {
            $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">[' . $row['media'] . ']</span>';
        }

        if (strlen($row['filter'])) {
            $label .= ' <span style="padding-left: 3px; color: #B3B3B3;">' . (($row['filterInvert'])
                ? '!'
                : '') . '[' . implode(',',
                                      deserialize($row['filterRule'],
                                                  true)) . ']</span>';
        }

        $image = 'system/modules/ThemePlus/assets/images/' . $row['type'] . '.png';

        return '<div>' . ($image
            ? $this->generateImage($image,
                                   $label,
                                   'style="vertical-align:-3px"') . ' '
            : '') . $label . "</div>\n";

    }

    protected function buildAsseticFilterOptions($type)
    {
        $options = array();


        $filterChain = FilterChainModel::findBy('type',
                                                $type,
                                                array('order' => 'type'));
        if ($filterChain) {
            while ($filterChain->next()) {
                $label = '[';
                $label .= $GLOBALS['TL_LANG']['tl_assetic_filter_chain']['types'][$filterChain->type]
                    ? : $filterChain->type;
                $label .= '] ';
                $label .= $filterChain->name;

                $GLOBALS['TL_LANG']['assetic']['chain:' . $filterChain->id] = $label;

                $options['chain'][] = 'chain:' . $filterChain->id;
            }
        }

        $filter = FilterModel::findAll(array('order' => 'type'));
        if ($filter) {
            while ($filter->next()) {
                if (!in_array($filter->type,
                              $GLOBALS['ASSETIC'][$type])
                ) {
                    continue;
                }

                $label = $GLOBALS['TL_LANG']['assetic'][$filter->type]
                    ? : $filter->type;

                if ($filter->note) {
                    $label .= ' [' . $filter->note . ']';
                }

                $GLOBALS['TL_LANG']['assetic']['filter:' . $filter->id] = $label;

                $options['filter'][] = 'filter:' . $filter->id;
            }
        }

        return $options;
    }
}
