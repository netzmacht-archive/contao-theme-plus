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

use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Asset\DatabaseAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Filter\FilterRulesFactory;

/**
 * Class AbstractAssetCollector.
 */
class AbstractAssetCollector
{
    /**
     * @var AsseticFactory
     */
    protected $asseticFactory;

    /**
     * @var FilterRulesFactory
     */
    protected $filterRulesFactory;

    public function __construct(AsseticFactory $asseticFactory, FilterRulesFactory $filterRulesFactory)
    {
        $this->asseticFactory     = $asseticFactory;
        $this->filterRulesFactory = $filterRulesFactory;
    }

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
            $asset = new DatabaseAsset(
                $model->row(),
                $type,
                $event->getRenderMode(),
                $this->asseticFactory,
                $this->filterRulesFactory
            );

            $event->append($asset, 100);
        }
    }

    /**
     * Check if asset is a local assets.
     *
     * @param string $javaScript Javascript path.
     *
     * @return bool
     */
    protected function isLocalAssets($javaScript)
    {
        preg_match('~^(http:|https:)?//.*~Ui', $javaScript, $matches);

        return empty($matches);
    }
}
