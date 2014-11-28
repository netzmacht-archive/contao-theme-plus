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

use Bit3\Contao\ThemePlus\Model\VariableModel;
use Bit3\Contao\ThemePlus\ThemePlus;

/**
 * Class Stylesheet
 */
class Variable
{
    /**
     * Get the variable name.
     */
    static public function getName($varValue, $dc)
    {
        $varValue = standardize($varValue);

        $objVariable = \Database::getInstance()
            ->prepare("SELECT * FROM tl_theme_plus_variable WHERE id!=? AND pid=? AND name=?")
            ->execute($dc->id, $dc->activeRecord->pid, $varValue);
        if ($objVariable->next()) {
            throw new Exception(
                sprintf(
                    $GLOBALS['TL_LANG']['ERR']['unique'],
                    $GLOBALS['TL_LANG']['tl_theme_plus_variable']['name'][0]
                )
            );
        }

        return $varValue;
    }

    /**
     * List an variable
     *
     * @param array
     *
     * @return string
     */
    static public function listVariables($row)
    {
        $variable = VariableModel::findByPk($row['id']);

        $label = '<strong>' . $variable->name . '</strong>: ' . ThemePlus::renderVariable($variable);

        switch ($variable->type) {
            case 'text':
                $image = 'assets/theme-plus/images/text.png';
                break;

            case 'url':
                $image = 'assets/theme-plus/images/url.png';
                break;

            case 'file':
                $image = 'files.gif';
                break;

            case 'color':
                $image = 'assets/theme-plus/images/color.png';
                break;

            case 'size':
                $image = 'assets/theme-plus/images/size.png';
                break;

            default:
                $image = '';
        }

        if ($image) {
            $image = \Image::getHtml(
                    $image,
                    $GLOBALS['TL_LANG']['tl_theme_plus_variable'][$variable->type][0],
                    'style="vertical-align:middle" title="' . specialchars(
                        $GLOBALS['TL_LANG']['tl_theme_plus_variable'][$variable->type][0]
                    ) . '"'
                )
                     . ' ';
        }

        return '<div>' . $image . $label . "</div>\n";
    }
}
