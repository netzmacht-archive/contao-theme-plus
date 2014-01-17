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

namespace Bit3\Contao\ThemePlus\Model;

/**
 * Class StylesheetModel
 */
class StylesheetModel extends \Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_theme_plus_stylesheet';


	/**
	 * Find all records by their primary keys
	 *
	 * @param array $arrPks     An array of primary key values
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null The model collection or null if the result is empty
	 */
	public static function findByPks($arrPks, array $arrOptions = array())
	{
		if (!is_array($arrPks) || empty($arrPks))
		{
			return null;
		}

		$arrOptions = array_merge($arrOptions, array
		(
			'column' => array(static::$strTable . '.' . static::$strPk . ' IN (' . rtrim(str_repeat('?,', count($arrPks)), ',') . ')'),
			'value'  => $arrPks,
			'return' => 'Collection'
		));

		return static::find($arrOptions);
	}
}
