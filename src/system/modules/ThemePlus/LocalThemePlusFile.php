<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2012 InfinitySoft <http://www.infinitysoft.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
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
