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

namespace Bit3\Contao\ThemePlus\DataContainer;

/**
 * Class Theme
 */
class Theme extends \Backend
{
    public function __construct()
    {
        parent::__construct();

        $this->import('BackendUser', 'User');
    }

    public function editStylesheet($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->User->isAdmin || $this->User->hasAccess('theme_plus_stylesheet', 'themes')) {
            return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
                $title
            ) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
        }
        return $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
    }

    public function editJavaScript($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->User->isAdmin || $this->User->hasAccess('theme_plus_javascript', 'themes')) {
            return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
                $title
            ) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
        }
        return $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
    }
}