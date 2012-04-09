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

?>