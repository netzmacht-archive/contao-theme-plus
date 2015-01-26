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

/**
 * Class upgrade_version5_0_0
 */
class upgrade_version5_0_0
{
    public function run()
    {
        $assetsCachePath = implode(DIRECTORY_SEPARATOR, [TL_ROOT, 'system', 'cache', 'assets']);

        if (!is_dir($assetsCachePath)) {
            mkdir($assetsCachePath, 0777, true);
        }
    }
}

$upgrade_version5_0_0 = new upgrade_version5_0_0();
$upgrade_version5_0_0->run();
