<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

namespace Bit3\Contao\ThemePlus;

class ThemePlusEvents
{
    /**
     * The STRIP_STATIC_DOMAIN event occurs when the static URL must be removed from an url.
     *
     * The event listener method receives a Bit3\Contao\ThemePlus\Event\StripStaticDomainEvent instance.
     *
     * @var string
     *
     * @api
     */
    const STRIP_STATIC_DOMAIN = 'theme-plus.strip-static-domain';

    /**
     * The ADD_STATIC_DOMAIN event occurs when the static URL must be added to an url.
     *
     * The event listener method receives a Bit3\Contao\ThemePlus\Event\AddStaticDomainEvent instance.
     *
     * @var string
     *
     * @api
     */
    const ADD_STATIC_DOMAIN = 'theme-plus.add-static-domain';

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
