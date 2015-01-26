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

namespace Bit3\Contao\ThemePlus\Model;

/**
 * Class JavaScriptModel
 */
class JavaScriptModel extends \Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected static $strTable = 'tl_theme_plus_javascript';


    /**
     * Find all records by their primary keys
     *
     * @param array $arrPks     An array of primary key values
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|null The model collection or null if the result is empty
     */
    public static function findByPks($arrPks, array $arrOptions = [])
    {
        if (!is_array($arrPks) || empty($arrPks)) {
            return null;
        }

        $arrOptions = array_merge(
            $arrOptions,
            [
                'column' => [
                    static::$strTable . '.' . static::$strPk . ' IN (' . rtrim(
                        str_repeat('?,', count($arrPks)),
                        ','
                    ) . ')'
                ],
                'value'  => $arrPks,
                'return' => 'Collection'
            ]
        );

        return static::find($arrOptions);
    }
}
