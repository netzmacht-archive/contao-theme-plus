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

namespace Bit3\Contao\ThemePlus\DataContainer;

/**
 * Class Stylesheet
 */
class Stylesheet extends File
{

    /**
     * @var \BackendUser
     */
    protected $User;

    /**
     * Check permissions to edit the table
     */
    public function checkPermission()
    {
        if ($this->User->isAdmin) {
            return;
        }

        if (!$this->User->hasAccess('theme_plus_stylesheet', 'themes')) {
            $this->log(
                'Not enough permissions to access the style sheets module',
                'tl_theme_plus_stylesheet checkPermission',
                TL_ERROR
            );
            $this->redirect('contao/main.php?act=error');
        }
    }

    public function rememberType($varValue)
    {
        \Session::getInstance()->set('THEME_PLUS_LAST_CSS_TYPE', $varValue);

        return $varValue;
    }

    public function getAsseticFilterOptions()
    {
        return $this->buildAsseticFilterOptions('css');
    }

    public function listFile($row)
    {
        return $this->listFileFor($row, 'theme_plus_stylesheets');
    }

    public function loadLayouts($value, $dataContainer)
    {
        return $this->loadLayoutsFor('theme_plus_stylesheets', $dataContainer);
    }

    public function saveLayouts($value, $dataContainer)
    {
        return $this->saveLayoutsFor('theme_plus_stylesheets', $value, $dataContainer);
    }
}
