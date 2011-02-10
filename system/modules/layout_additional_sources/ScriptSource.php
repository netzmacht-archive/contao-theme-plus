<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Layout Additional Sources
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    LGPL
 * @filesource
 */

/**
 * Class ScriptSource
 *
 * Front end content element "script_source".
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
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
	 * @param object
	 * @return string
	 */
	public function __construct(Database_Result $objElement)
	{
		parent::__construct();

		$this->arrData = $objElement->row();
	}


	/**
	 * Set an object property
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 * @param string
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
		if (TL_MODE == 'BE')
		{
			$this->import('Database');
			$arrScriptSource = deserialize($this->script_source);
			if (count($arrScriptSource))
			{
				$objSource = $this->Database->execute("SELECT * FROM tl_additional_source WHERE id IN (" . implode(',', array_map('intval', $arrScriptSource)) . ")");
				$strBuffer = '';
				while ($objSource->next())
				{
					$strType = $objSource->type;
					$label = ' ' . $objSource->$strType;
					
					if (strlen($objSource->cc)) {
						$label .= ' <span style="color: #B3B3B3;">[' . $objSource->cc . ']</span>';
					}
					
					if (strlen($objSource->media)) {
						$arrMedia = unserialize($objSource->media);
						if (count($arrMedia)) {
							$label .= ' <span style="color: #B3B3B3;">[' . implode(', ', $arrMedia) . ']</span>';
						}
					}
					
					$strBuffer = $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label . '<br/>';
				}
				return $strBuffer;
			}
			return $GLOBALS['TL_LANG']['MSC']['noResult'];
		}
		
		$this->import('LayoutAdditionalSources');
		return implode("\n", $this->LayoutAdditionalSources->generateIncludeHtml(deserialize($this->script_source, true))) . "\n";
	}
}

?>