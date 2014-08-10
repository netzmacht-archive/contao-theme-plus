<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\ThemePlus;

class ThemePlusEvents
{
	/**
	 * The COLLECT_STYLESHEET_ASSETS event occurs when the stylesheet assets for a page should be collected.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\CollectAssetsEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const COLLECT_STYLESHEET_ASSETS = 'theme-plus.collect-stylesheet-assets';

	/**
	 * The COLLECT_HEAD_JAVASCRIPT_ASSETS event occurs when the javascript assets for a page should be collected.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\CollectAssetsEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const COLLECT_HEAD_JAVASCRIPT_ASSETS = 'theme-plus.collect-head-javascript-assets';

	/**
	 * The COLLECT_BODY_JAVASCRIPT_ASSETS event occurs when the javascript assets for a page should be collected.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\CollectAssetsEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const COLLECT_BODY_JAVASCRIPT_ASSETS = 'theme-plus.collect-body-javascript-assets';
}
