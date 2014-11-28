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

class ConditionParser
{
    public function parse($filterRules)
    {
        $or = [];

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
