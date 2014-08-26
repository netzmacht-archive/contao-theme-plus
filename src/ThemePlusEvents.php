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
	 * The GENERATE_ASSET_PATH event occurs when the script path for the compiled asset must be generated.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const GENERATE_ASSET_PATH = 'theme-plus.generate-asset-path';

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
	 * The ORGANIZE_STYLESHEET_ASSETS event occurs when the stylesheet assets must be organized into a specific order.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const ORGANIZE_STYLESHEET_ASSETS = 'theme-plus.organize-stylesheet-assets';

	/**
	 * The RENDER_STYLESHEET_HTML event occurs when the stylesheet asset is rendered into html.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const RENDER_STYLESHEET_HTML = 'theme-plus.render-stylesheet-html';

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

	/**
	 * The ORGANIZE_JAVASCRIPT_ASSETS event occurs when the javascript assets must be organized into a specific order.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const ORGANIZE_JAVASCRIPT_ASSETS = 'theme-plus.organize-javascript-assets';

	/**
	 * The RENDER_JAVASCRIPT_HTML event occurs when the javascript asset is rendered into html.
	 *
	 * The event listener method receives a Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent instance.
	 *
	 * @var string
	 *
	 * @api
	 */
	const RENDER_JAVASCRIPT_HTML = 'theme-plus.render-javascript-html';
}
