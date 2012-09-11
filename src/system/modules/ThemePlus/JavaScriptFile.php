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
 * A javascript file.
 */
interface JavaScriptFile
{
	/**
	 * Set the include position
	 *
	 * @abstract
	 *
	 * @param string $strPosition
	 *
	 * @return void
	 */
	public function setPosition($strPosition);


	/**
	 * Return the include position.
	 *
	 * @abstract
	 * @return string
	 */
	public function getPosition();
}
