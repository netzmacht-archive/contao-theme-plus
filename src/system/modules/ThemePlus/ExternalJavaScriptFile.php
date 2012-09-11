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
 * Class ExternalJavaScriptFile
 */
class ExternalJavaScriptFile extends ExternalThemePlusFile implements JavaScriptFile
{
	/**
	 * The include position
	 *
	 * @var string
	 */
	protected $strPosition = '';


	/**
	 * Create a new external javascript
	 *
	 * @param string $strUrl
	 */
	public function __construct($strUrl)
	{
		parent::__construct($strUrl);
	}


	/**
	 * Set the include position.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function setPosition($strPosition)
	{
		$this->strPosition = $strPosition;
	}


	/**
	 * Get the include position.
	 *
	 * @return string
	 */
	public function getPosition()
	{
		return $this->strPosition;
	}


	/**
	 * @see ThemePlusFile::getDebugComment
	 * @return string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus()) {
			return '<!-- external url: ' . $this->getUrl() . ', position: ' . $this->getPosition() . ' -->' . "\n";
		}
		return '';
	}


	/**
	 * @see ThemePlusFile::getIncludeHtml
	 * @return string
	 */
	public function getIncludeHtml($blnLazy = false)
	{
		global $objPage;

		// get the file
		$strFile = $this->getUrl();

		// return html code
		if ($blnLazy) {
			return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '">' . $this->ThemePlus->wrapJavaScriptLazyInclude() . '</script>');
		}
		else
		{
			return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . specialchars($strFile) . '"></script>');
		}
	}


	/**
	 * Convert into a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getUrl() . '|' . $this->getCc() . '|' . $this->getPosition();
	}
}
