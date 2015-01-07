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

namespace Bit3\Contao\ThemePlus\Collector;

use Bit3\Contao\ThemePlus\Asset\DatabaseAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;

/**
 * Class AbstractAssetCollector.
 */
class AbstractAssetCollector
{
    /**
     * Append models as database assets to the event collection.
     *
     * @param CollectAssetsEvent $event      The collect event.
     * @param \Model\Collection  $collection The model collection.
     * @param string             $type       The file type.
     *
     * @return void
     */
    protected function appendDatabaseAssets(CollectAssetsEvent $event, \Model\Collection $collection, $type)
    {
        foreach ($collection as $model) {
            $asset = new DatabaseAsset($model->row(), $type, $event->getRenderMode());

            $event->append($asset, 100);
        }
    }
}
