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
 * A css file.
 */
interface CssFile
{
	/**
	 * Set the media query.
	 *
	 * @abstract
	 *
	 * @param string $strMedia
	 */
	public function setMedia($strMedia);


	/**
	 * Get the media query.
	 *
	 * @abstract
	 * @return string
	 */
	public function getMedia();
}
