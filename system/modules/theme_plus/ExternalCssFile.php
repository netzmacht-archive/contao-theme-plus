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
 * Class ExternalCssFile
 */
class ExternalCssFile extends ExternalThemePlusFile {

	/**
	 * The media selectors.
	 */
	protected $strMedia;


	public function __construct($strUrl, $strMedia = '', $strCc = '')
	{
		parent::__construct($strUrl, $strCc);
		$this->strMedia = $strMedia;
	}


	public function getMedia()
	{
		return $this->strMedia;
	}


	public function getGlobalVariableCode()
	{
		return $this->getUrl() . (strlen($this->strMedia) ? '|' . $this->strMedia : '') . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}


	public function getIncludeHtml()
	{
		global $objPage;

		// get the file
		$strUrl = $this->getUrl();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />');
	}


	/**
	 * Convert into a string.
	 */
	public function __toString()
	{
		return $this->getUrl() . '|' . $this->getMedia() . '|' . $this->getCc();
	}

}

?>