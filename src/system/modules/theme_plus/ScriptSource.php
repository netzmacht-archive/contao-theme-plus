<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Theme+
 * Copyright (C) 2010,2011 InfinitySoft <http://www.infinitysoft.de>
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 * @license    LGPL
 */

/**
 * Class ScriptSource
 *
 * Front end content element "script_source".
 */
class ScriptSource extends Frontend
{
	/**
	 * Current record
	 * @var array
	 */
	protected $arrData = array();


	/**
	 * Initialize the object
	 *
	 * @param object
	 *
	 * @return string
	 */
	public function __construct(Database_Result $objElement)
	{
		parent::__construct();

		$this->arrData = $objElement->row();
	}


	/**
	 * Set an object property
	 *
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		return $this->arrData[$strKey];
	}


	/**
	 * Generate frontend element
	 */
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$this->import('Database');
			$arrScriptSource = deserialize($this->script_source);
			if (count($arrScriptSource)) {
				$objSource = $this->Database->execute("SELECT * FROM tl_theme_plus_file WHERE id IN (" . implode(',', array_map('intval', $arrScriptSource)) . ")");
				$strBuffer = '';
				while ($objSource->next())
				{
					$strType = $objSource->type;
					$label   = ' ' . $objSource->$strType;

					$strBuffer .= $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label . '<br/>';
				}
				return $strBuffer;
			}
			return $GLOBALS['TL_LANG']['MSC']['noResult'];
		}

		$this->import('ThemePlus');
		return implode("\n", $this->ThemePlus->includeFiles(deserialize($this->script_source, true))) . "\n";
	}
}
