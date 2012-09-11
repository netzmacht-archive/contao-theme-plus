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
	 *
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
				return $this->getEmbeddedHtml();

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
	 *
	 * @return string
	 */
	protected function wrapCc($strCode)
	{
		if (strlen($this->strCc)) {
			return '<!--[if ' . $this->strCc . ']>' . $strCode . '<![endif]-->';
		}
		return $strCode;
	}
}
