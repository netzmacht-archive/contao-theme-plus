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
 * Class LocalThemePlusFile
 */
abstract class LocalThemePlusFile extends ThemePlusFile
{
	/**
	 * Get a file from path. 
	 */
	public static function create()
	{
		$args = func_get_args();
		$strFile = $args[0];
		if (file_exists(TL_ROOT . '/' . $strFile))
		{
			$objFile = new File($strFile);
			switch ($objFile->extension)
			{
				case 'js':
					return new LocalJavaScriptFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : false);
				
				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less'])
					{
						return new LocalCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
					}
					
				case 'less':
					return new LocalLessCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
			}
		}
		return false;
	}
	

	/**
	 * The origin file path.
	 */
	protected $strOriginFile;
	
	
	/**
	 * The corresponding theme.
	 */
	protected $objTheme;
	
	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strOriginFile, $strCc = '', $objTheme = false)
	{
		if (!file_exists(TL_ROOT . '/' . $strOriginFile))
		{
			throw new Exception('File does not exists: ' . $strOriginFile);
		}
		
		parent::__construct($strCc);
		$this->strOriginFile = $strOriginFile;
		$this->objTheme = $objTheme;
	}
	
	
	/**
	 * Get a debug comment string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($this->ThemePlus->getBELoginStatus())
		{
			return '<!-- local file: ' . $this->getOriginFile() . ' -->' . "\n";
		}
		return '';
	}
	
	
	/**
	 * Get the original file path relative to TL_ROOT.
	 */
	public function getOriginFile()
	{
		return $this->strOriginFile;
	}
	
	
	/**
	 * Get the file path relative to TL_ROOT
	 */
	public abstract function getFile();
	
	
	public function getGlobalVariableCode()
	{
		return $this->getFile() . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}
	
		
	public function __get($k)
	{
		switch ($k)
		{
		case 'origin':
			return $this->getOriginFile();
			
		case 'file':
		case 'path':
			return $this->getFile();
		
		default:
			return parent::__get($k);
		}
	}
	
}

?>