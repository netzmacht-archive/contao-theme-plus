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
		$strFile      = parse_url($strUrl, PHP_URL_PATH);
		$strExtension = preg_replace('#.*\.(\w+)$#', '$1', $strFile);
		if ($strExtension) {
			switch (strtolower($strExtension))
			{
				case 'js':
					return new ExternalJavaScriptFile($strFile);

				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less']) {
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
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus()) {
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
	 *
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
