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
 * Class NoneMinimizer
 *
 * wrapper class for the less css compiler (http://lesscss.org)
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Compression API
 */
class NoneMinimizer extends AbstractMinimizer
{
	/**
	 * (non-PHPdoc)
	 * @see Minimizer::minimizeCode($strCode)
	 */
	public function minimizeCode($strCode)
	{
		return $strCode;
	}
}
