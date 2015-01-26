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

namespace Bit3\Contao\ThemePlus\Event;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

class CollectAssetsEvent extends LayoutAwareEvent
{
    /**
     * @var FilterCollection|FilterInterface[]|null
     */
    protected $defaultFilters;

    /**
     * @var AssetInterface[][]
     */
    protected $sortedAssets = [];

    /**
     * @var AssetCollection
     */
    protected $assets;

    /**
     * @var bool
     */
    protected $dirty = true;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        FilterCollection $defaultFilters = null
    ) {
        parent::__construct($renderMode, $page, $layout);
        $this->defaultFilters = $defaultFilters;
    }

    /**
     * @return FilterCollection|FilterInterface[]|null
     */
    public function getDefaultFilters()
    {
        return $this->defaultFilters;
    }

    /**
     * @return AssetCollectionInterface
     */
    public function getAssets()
    {
        if ($this->dirty) {
            $this->assets = new AssetCollection([], [], TL_ROOT);

            ksort($this->sortedAssets);
            foreach ($this->sortedAssets as $assets) {
                foreach ($assets as $asset) {
                    $this->assets->add($asset);
                }
            }

            $this->dirty = false;
        }

        return $this->assets;
    }

    /**
     * Prepend an asset to the collection.
     *
     * @param AssetInterface $asset
     * @param int            $position
     *
     * @return static
     */
    public function prepend(AssetInterface $asset, $position = 0)
    {
        $hash = spl_object_hash($asset);

        foreach ($this->sortedAssets as $assets) {
            if (isset($assets[$hash])) {
                throw new \InvalidArgumentException(
                    sprintf('Asset %s [%s] was already collected', $hash, get_class($asset))
                );
            }
        }

        $this->dirty = true;

        if (isset($this->sortedAssets[$position])) {
            $this->sortedAssets[$position] = array_merge(
                [$hash => $asset],
                $this->sortedAssets[$position]
            );
        } else {
            $this->sortedAssets[$position] = [$hash => $asset];
        }

        return $this;
    }

    /**
     * Append an asset to the collection.
     *
     * @param AssetInterface $asset
     * @param int            $position
     *
     * @return static
     */
    public function append(AssetInterface $asset, $position = 0)
    {
        $hash = spl_object_hash($asset);

        foreach ($this->sortedAssets as $assets) {
            if (isset($assets[$hash])) {
                throw new \InvalidArgumentException(
                    sprintf('Asset %s [%s] was already collected', $hash, get_class($asset))
                );
            }
        }

        $this->dirty = true;

        if (isset($this->sortedAssets[$position])) {
            $this->sortedAssets[$position][$hash] = $asset;
        } else {
            $this->sortedAssets[$position] = [$hash => $asset];
        }

        return $this;
    }

    /**
     * Remove an asset from the collection.
     *
     * @param AssetInterface $asset
     *
     * @return static
     */
    public function remove(AssetInterface $asset)
    {
        $hash = spl_object_hash($asset);

        foreach ($this->sortedAssets as $assets) {
            if (isset($assets[$hash])) {
                unset($assets[$hash]);
            }
        }

        $this->dirty = true;

        return $this;
    }
}
