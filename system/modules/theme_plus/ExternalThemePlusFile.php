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
 * Class ExternalThemePlusFile
 */
abstract class ExternalThemePlusFile extends ThemePlusFile
{
	/**
	 * Get a file from path. 
	 */
	public static function create()
	{
		$args = func_get_args();
		$strUrl = $args[0];
		$strFile = parse_url($strUrl, PHP_URL_PATH);
		$strExtension = preg_replace('#.*\.(\w+)$#', '$1', $strFile);
		if ($strExtension)
		{
			switch (strtolower($strExtension))
			{
				case 'js':
					return new ExternalJavaScriptFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : false);
				
				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less'])
					{
						return new ExternalCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
					}
					
				case 'less':
					return new ExternalLessCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
			}
		}
		return false;
	}


	/**
	 * The origin file path.
	 */
	protected $strUrl;

	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strUrl, $strCc = '')
	{
		parent::__construct($strCc);
		$this->strUrl = $strUrl;
	}
	
	
	/**
	 * Get a debug comment string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($this->ThemePlus->getBELoginStatus())
		{
			return '<!-- external url: ' . $this->getUrl() . ' -->' . "\n";
		}
		return '';
	}
	
	
	/**
	 * Get the url.
	 */
	public function getUrl()
	{
		return $this->strUrl;
	}
	
	
	public function getGlobalVariableCode()
	{
		return $this->getUrl() . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}
	
	
	public function getEmbededHtml()
	{
		return $this->getIncludeHtml();
	}
	
	
	public function isAggregateable()
	{
		return false;
	}
	
	
	public function __get($k)
	{
		switch ($k)
		{
		case 'url':
			return $this->getUrl();
		
		default:
			return parent::__get($k);
		}
	}
	
}

?>