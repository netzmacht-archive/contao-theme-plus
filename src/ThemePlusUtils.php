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

namespace Bit3\Contao\ThemePlus;

class ThemePlusUtils
{
    /**
     * Wrap the conditional comment around.
     *
     * @param string $html The html to wrap around.
     * @param string $cc   The cc that should wrapped.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public static function wrapCc($html, $cc)
    {
        if (strlen($cc)) {
            return '<!--[if ' . $cc . ']>' . $html . '<![endif]-->';
        }
        return $html;
    }
}
