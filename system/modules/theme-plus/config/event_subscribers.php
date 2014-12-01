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


return [
    new Bit3\Contao\ThemePlus\Collector\StylesheetCollectorSubscriber(),
    new Bit3\Contao\ThemePlus\Collector\JavaScriptCollectorSubscriber(),
    new Bit3\Contao\ThemePlus\Renderer\StylesheetRendererSubscriber(),
    new Bit3\Contao\ThemePlus\Renderer\JavaScriptRendererSubscriber(),
    new Bit3\Contao\ThemePlus\Organizer\AssetOrganizerSubscriber(),
    new Bit3\Contao\ThemePlus\StaticUrlSubscriber(),
];
