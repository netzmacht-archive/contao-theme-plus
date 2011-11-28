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
 * Class ThemePlusFile
 */
abstract class ThemePlusFile extends Controller
{
	/**
	 * The ThemePlus object
	 *
	 * @var ThemePlus
	 */
	protected $ThemePlus;


	/**
	 * The conditional comment.
	 *
	 * @var string
	 */
	protected $strCc = '';


	/**
	 * Create a new Theme+ file
	 *
	 * @param string $strCc
	 */
	public function __construct()
	{
		$this->import('ThemePlus');
	}


	/**
	 * Get a debug comment string
	 *
	 * @return string
	 */
	protected abstract function getDebugComment();


	/**
	 * Set the conditional comment.
	 *
	 * @param string $strCc
	 */
	public function setCc($strCc)
	{
		$this->strCc = $strCc;
	}
	

	/**
	 * Return the conditional comment.
	 *
	 * @return string
	 */
	public function getCc()
	{
		return $this->strCc;
	}


	/**
	 * Get a code that is compatible with TL_CSS and TL_JAVASCRIPT
	 *
	 * @return string
	 */
	public abstract function getGlobalVariableCode();


	/**
	 * Get embedded html code
	 *
	 * @return string
	 */
	public abstract function getEmbeddedHtml($blnLazy = false);


	/**
	 * Get included html code
	 *
	 * @return string
	 */
	public abstract function getIncludeHtml($blnLazy = false);


	/**
	 * Gives the information, if this file can be aggregated.
	 *
	 * @return true
	 */
	public function isAggregateable()
	{
		return strlen($this->strCc) ? false : true;
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
		case 'cc':
			return $this->getCc();

		case 'globalVariable':
			return $this->getGlobalVariableCode();

		case 'embed':
			return $this->getEmbededHtml();

		case 'include':
			return $this->getIncludeHtml();

		case 'aggregate':
			return $this->isAggregateable();
		}
	}


	/**
	 * Wrap the conditional comment arround.
	 *
	 * @param string $strCode
	 * @return string
	 */
	protected function wrapCc($strCode)
	{
		if (strlen($this->strCc))
		{
			return '<!--[if ' . $this->strCc . ']>' . $strCode . '<![endif]-->';
		}
		return $strCode;
	}
}

?>