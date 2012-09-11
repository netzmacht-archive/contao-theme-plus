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
 * Class ExternalLessCssFile
 */
class ExternalLessCssFile extends ExternalCssFile
{

	/**
	 * Create a new css file object.
	 *
	 * @param string $strUrl
	 */
	public function __construct($strUrl)
	{
		parent::__construct($strUrl);

		// import the Theme+ master class
		$this->import('ThemePlus');
	}


	/**
	 * @see ThemePlusFile::getIncludeHtml
	 * @return string
	 */
	public function getIncludeHtml($blnLazy = false)
	{
		global $objPage;

		// add client side javascript
		if ($this->ThemePlus->getBELoginStatus()) {
			$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.development.js';
		}
		else
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.js';
		}

		// get the file
		$strUrl = $this->getUrl();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />');
	}

}
