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

namespace ThemePlus\Hybrid;

/**
 * Class ScriptSource
 *
 * Front end content element "script_source".
 */
class JavaScript extends \Frontend
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
