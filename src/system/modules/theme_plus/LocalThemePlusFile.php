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
	 *
	 * @return LocalJavaScriptFile|LocalCssFile|LocalLessCssFile|false
	 */
	public static function create($strFile)
	{
		if (file_exists(TL_ROOT . '/' . ($strFile[0] == '!' ? substr($strFile, 1) : $strFile))) {
			$objFile = new File($strFile[0] == '!' ? substr($strFile, 1) : $strFile);
			switch ($objFile->extension)
			{
				case 'js':
					return new LocalJavaScriptFile($strFile);

				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less']) {
						return new LocalCssFile($strFile);
					}

				case 'less':
					return new LocalLessCssFile($strFile);
			}
		}
		return false;
	}


	/**
	 * The aggregation mode
	 *
	 * @var string
	 */
	protected $strAggregation = 'global';


	/**
	 * The current aggregation scope.
	 *
	 * @var string
	 */
	protected $strAggregationScope = 'global';


	/**
	 * The origin file path.
	 *
	 * @var string
	 */
	protected $strOriginFile;


	/**
	 * The corresponding theme.
	 *
	 * @var Database_Result
	 */
	protected $objTheme;


	/**
	 * Create a new local file object.
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param Database_Result
	 */
	public function __construct($strOriginFile)
	{
		if (!file_exists(TL_ROOT . '/' . $strOriginFile)) {
			throw new Exception('File does not exists: ' . $strOriginFile);
		}

		parent::__construct();
		$this->strOriginFile = $strOriginFile;
	}


	/**
	 * @see ThemePlusFile::getDebugComment
	 * @return string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus()) {
			return '<!-- local file: ' . $this->getOriginFile() . ', aggregation: ' . $this->getAggregation() . ', scope: ' . $this->getAggregationScope() . ' -->' . "\n";
		}
		return '';
	}


	/**
	 * Get the original file path relative to TL_ROOT.
	 *
	 * @returns tring
	 */
	public function getOriginFile()
	{
		return $this->strOriginFile;
	}


	/**
	 * Set the aggregation mode.
	 *
	 * @param string $strAggregation
	 */
	public function setAggregation($strAggregation)
	{
		$this->strAggregation = $strAggregation;
	}


	/**
	 * Get the aggregation mode.
	 *
	 * @return string
	 */
	public function getAggregation()
	{
		return $this->strAggregation;
	}


	/**
	 * Set the current aggregation scope.
	 *
	 * @param string $strAggregationScope
	 */
	public function setAggregationScope($strAggregationScope)
	{
		$this->strAggregationScope = $strAggregationScope;
	}


	/**
	 * Get the current aggregation level.
	 *
	 * @return string
	 */
	public function getAggregationScope()
	{
		return $this->strAggregationScope;
	}


	/**
	 * Set the theme object.
	 *
	 * @param Database_Result $objTheme
	 *
	 * @return void
	 */
	public function setTheme(Database_Result $objTheme)
	{
		$this->objTheme = $objTheme;
	}


	/**
	 * Get the theme object.
	 *
	 * @return Database_Result|null
	 */
	public function getTheme()
	{
		return $this->objTheme;
	}


	/**
	 * Get the file path relative to TL_ROOT
	 *
	 * @return string
	 */
	public abstract function getFile();


	/**
	 * @see ThemePlusFile::getGlobalVariableCode
	 * @return string
	 */
	public function getGlobalVariableCode()
	{
		return $this->getFile() . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}


	/**
	 * @see ThemePlusFile::isAggregateable
	 * @return bool
	 */
	public function isAggregateable()
	{
		return $this->getAggregation() == 'never' ? false : parent::isAggregateable();
	}


	/**
	 * Magic getter
	 *
	 * @param string $k
	 *
	 * @return mixed
	 */
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
