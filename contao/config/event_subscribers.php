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

/** @var \Pimple $container */
global $container;

return [
    $container['theme-plus-cache-generator-subscriber'],
    $container['theme-plus-stylesheet-collector-subscriber'],
    $container['theme-plus-javascript-collector-subscriber'],
    $container['theme-plus-stylesheet-renderer-subscriber'],
    $container['theme-plus-javascript-renderer-subscriber'],
    $container['theme-plus-asset-organizer-subscriber'],
    $container['theme-plus-static-url-subscriber'],
];
