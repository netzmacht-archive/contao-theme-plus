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

use
\Assetic\Model\FilterModel;
use
\Assetic\Model\FilterChainModel;

/**
 * Class Collection
 */
class Collection
    extends \Backend
{
    /**
     * List an collection
     *
     * @param array
     *
     * @return string
     */
    public function listCollection($row)
    {


        return '<div>' . 'foo' . "</div>\n";

    }

    protected function getAsseticFilter($type)
    {
        $options = array();


        $objFilterChain = FilterChainModel::findBy('type',
                                                   $type,
                                                   array('order' => 'type'));
        while ($objFilterChain->next()) {
            $label = '[';
            $label .= $GLOBALS['TL_LANG']['tl_assetic_filter_chain']['types'][$objFilterChain->type]
                ? : $objFilterChain->type;
            $label .= '] ';
            $label .= $objFilterChain->name;

            $GLOBALS['TL_LANG']['assetic']['chain:' . $objFilterChain->id] = $label;

            $options['chain']['chain:' . $objFilterChain->id] = $label;
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

                $options['filter'] = 'filter:' . $filter->id;
            }
        }

        return $options;
    }
}
