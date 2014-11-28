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
 * Class JavaScript
 */
class JavaScript extends File
{
    /**
     * Check permissions to edit the table
     */
    public function checkPermission()
    {
        if ($this->User->isAdmin) {
            return;
        }

        if (!$this->User->hasAccess('theme_plus_javascript', 'themes')) {
            $this->log(
                'Not enough permissions to access the Theme+ javascript module',
                'tl_theme_plus_javascript checkPermission',
                TL_ERROR
            );
            $this->redirect('contao/main.php?act=error');
        }
    }

    public function rememberType($varValue)
    {
        \Session::getInstance()->set('THEME_PLUS_LAST_JS_TYPE', $varValue);

        return $varValue;
    }

    public function getAsseticFilterOptions()
    {
        return $this->buildAsseticFilterOptions('js');
    }

    public function listFile($row)
    {
        return $this->listFileFor($row, 'theme_plus_javascripts');
    }

    public function loadLayouts($value, $dc)
    {
        return $this->loadLayoutsFor('theme_plus_javascripts', $dc);
    }

    public function saveLayouts($value, $dc)
    {
        return $this->saveLayoutsFor('theme_plus_javascripts', $value, $dc);
    }
}