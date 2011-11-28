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
		$this->strCode = $strCode;
		$this->strReference = $strReference;

		$strHash = md5($strCode);
		$strFile = 'system/scripts/stylesheet-' . $strReference . '-' . substr($strHash, 0, 8) . '.js';

		if (!file_exists(TL_ROOT . '/' . $strFile))
		{
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
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus())
		{
			return '<!-- css code: ' . $this->strReference . ', aggregation: ' . $this->getAggregation() . ', scope: ' . $this->getAggregationScope() . ' -->' . "\n";
		}
		return '';
	}
}
