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
 * Class FileAggregator
 */
abstract class FileAggregator extends LocalThemePlusFile
{
	/**
	 * The scope of this aggregator
	 *
	 * @var string
	 */
	protected $strScope;


	/**
	 * Create a new aggregator
	 *
	 * @param string $strScope
	 */
	public function __construct($strScope)
	{
		$this->strScope = $strScope;
	}


	/**
	 * Get the scope of this aggregator
	 *
	 * @return string
	 */
	public function getScope()
	{
		return $this->strScope;
	}


	/**
	 * @see ThemePlusFile::getDebugComment
	 * @return string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus()) {
			return "<!--\nfile aggregation:\n - " . implode("\n - ", array_map(create_function('$objFile', 'return $objFile->getFile();'), $this->arrFiles)) . ', scope: ' . $this->getScope() . "\n-->\n";
		}
		return '';
	}
}
