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

namespace Bit3\Contao\ThemePlus;

class ConditionParser
{
    public function parse($filterRules)
    {
        $or        = [];

        foreach ($filterRules as $filterRule) {
            $and = [];

            if ($filterRule['platform']) {
                switch ($filterRule['platform']) {
                    case 'desktop':
                        $and[] = 'environment.isDesktop()';
                        break;

                    case 'tablet-or-mobile':
                        $and[] = '(environment.isTablet() OR environment.isMobile())';
                        break;

                    case 'tablet':
                        $and[] = 'environment.isTablet()';
                        break;

                    case 'mobile':
                        $and[] = 'environment.isMobile()';
                        break;
                }
            }

            if ($filterRule['system']) {

            }

            $or[] = '(' . implode(' AND ', $and) . ')';
        }

        return '(' . implode(' OR ', $or) . ')';
    }
}
