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
	 *
	 * @return ExternalJavaScriptFile|ExternalCssFile|ExternalLessCssFile|false
	 */
	public static function create($strUrl)
	{
		$strFile = parse_url($strUrl, PHP_URL_PATH);
		$strExtension = preg_replace('#.*\.(\w+)$#', '$1', $strFile);
		if ($strExtension)
		{
			switch (strtolower($strExtension))
			{
				case 'js':
					return new ExternalJavaScriptFile($strFile);
				
				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less'])
					{
						return new ExternalCssFile($strFile);
					}
					
				case 'less':
					return new ExternalLessCssFile($strFile);
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
	 *
	 * @param string $strUrl
	 */
	public function __construct($strUrl)
	{
		parent::__construct();
		$this->strUrl = $strUrl;
	}
	
	
	/**
	 * @see ThemePlusFile::getDebugComment
	 * @return string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus())
		{
			return '<!-- external url: ' . $this->getUrl() . ' -->' . "\n";
		}
		return '';
	}
	
	
	/**
	 * Get the url.
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->strUrl;
	}
	

	/**
	 * @see ThemePlusFile::getGlobalVariableCode
	 * @return string
	 */
	public function getGlobalVariableCode()
	{
		return $this->getUrl() . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}
	

	/**
	 * @see ThemePlusFile::getEmbeddedHtml
	 * @return string
	 */
	public function getEmbeddedHtml($blnLazy = false)
	{
		return $this->getIncludeHtml($blnLazy);
	}
	

	/**
	 * @see ThemePlusFile::isAggregateable
	 * @return bool
	 */
	public function isAggregateable()
	{
		return false;
	}
	

	/**
	 * Magic getter
	 *
	 * @param string $k
	 * @return mixed
	 */
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
