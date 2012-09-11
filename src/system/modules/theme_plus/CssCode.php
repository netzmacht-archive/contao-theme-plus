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
 * Class CssFile
 */
class CssCode extends LocalCssFile
{
	/**
	 * The javascript code
	 *
	 * @var string
	 */
	protected $strCode;


	/**
	 * A reference to identify
	 *
	 * @var string
	 */
	protected $strReference;


	/**
	 * Create a new javascript code object.
	 *
	 * @param string $strCode
	 */
	public function __construct($strCode, $strReference = 'undefined')
	{
		$this->strCode      = $strCode;
		$this->strReference = $strReference;

		$strHash = md5($strCode);
		$strFile = 'system/scripts/stylesheet-' . $strReference . '-' . substr($strHash, 0, 8) . '.css';

		if (!file_exists(TL_ROOT . '/' . $strFile)) {
			$objFile = new File($strFile);
			$objFile->write($strCode);
			$objFile->close();
		}

		parent::__construct($strFile);
	}


	/**
	 * Get the javascript code.
	 *
	 * @return string
	 */
	public function getCode()
	{
		return $this->strCode;
	}


	/**
	 * @see ThemePlusFile::getDebugComment
	 * @return string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus()) {
			return '<!-- css code: ' . $this->strReference . ', aggregation: ' . $this->getAggregation() . ', scope: ' . $this->getAggregationScope() . ' -->' . "\n";
		}
		return '';
	}
}
