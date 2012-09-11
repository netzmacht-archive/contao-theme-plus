<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * Class ExternalCssFile
 */
class ExternalCssFile extends ExternalThemePlusFile implements CssFile
{
	/**
	 * The media selectors.
	 * @var string
	 */
	protected $strMedia = '';


	/**
	 * Create new external css file.
	 *
	 * @param string $strUrl
	 * @param string $strMedia
	 * @param string $strCc
	 */
	public function __construct($strUrl)
	{
		parent::__construct($strUrl);
	}


	/**
	 * @see CssFile::setMedia
	 */
	public function setMedia($strMedia)
	{
		$this->strMedia = $strMedia;
	}


	/**
	 * @see CssFile::getMedia
	 * @return string
	 */
	public function getMedia()
	{
		return $this->strMedia;
	}


	/**
	 * @see ThemePlusFile::getGlobalVariableCode
	 * @return string
	 */
	public function getGlobalVariableCode()
	{
		return $this->getUrl() . (strlen($this->strMedia) ? '|' . $this->strMedia : '') . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}


	/**
	 * @see ThemePlusFile::getIncludeHtml
	 * @return string
	 */
	public function getIncludeHtml($blnLazy = false)
	{
		global $objPage;

		// get the file
		$strUrl = $this->getUrl();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />');
	}


	/**
	 * Convert into a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getUrl() . '|' . $this->getMedia() . '|' . $this->getCc();
	}

}
