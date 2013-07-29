<?php

/**
 * Assetic for Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG
 *
 * @package Assetic
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace ThemePlus\Filter;

use ThemePlus\Filter\ContaoReplaceVariableFilter;
use ContaoAssetic\DefaultFilterFactory;
use ContaoAssetic\FilterFactory;

class ThemePlusFilterFactory
    extends DefaultFilterFactory
    implements FilterFactory
{
    public function createFilter(array $filterConfig)
    {
        $filter = null;

        switch ($filterConfig['type']) {
            case 'contaoReplaceVariable':
                $filter = new ContaoReplaceVariableFilter();
                break;
        }
        
        if($filter === null)
        {
            $filter = parent::createFilter($filterConfig);
        }

        return $filter;
    }
}
