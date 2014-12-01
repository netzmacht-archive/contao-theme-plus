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
use Bit3\Contao\ThemePlus\ConditionCompiler;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;

class AbstractAssetCollector
{
    protected function appendDatabaseAssets(CollectAssetsEvent $event, \Model\Collection $collection, $type)
    {
        global $container;

        /** @var ConditionCompiler $conditionCompiler */
        $conditionCompiler = $container['theme-plus-condition-compiler'];

        foreach ($collection as $model) {
            if (
                $model->filter
                && !$conditionCompiler->evaluate(deserialize($model->filterRules, true))
            ) {
                continue;
            }

            $asset = new DatabaseAsset($model->row(), $type);
            $event->append($asset, 100);
        }
    }
}
