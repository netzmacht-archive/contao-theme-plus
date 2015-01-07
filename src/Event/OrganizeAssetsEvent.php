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

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;

class OrganizeAssetsEvent extends AssetCollectionAwareEvent
{
    /**
     * @var AssetCollectionInterface|AssetInterface[]|null
     */
    protected $organizedAssets;

    /**
     * @return AssetCollectionInterface|AssetInterface[]|null
     */
    public function getOrganizedAssets()
    {
        return $this->organizedAssets;
    }

    /**
     * @param AssetCollectionInterface|AssetInterface[]|null $organizedAssets
     *
     * @return static
     */
    public function setOrganizedAssets(AssetCollectionInterface $organizedAssets = null)
    {
        $this->organizedAssets = $organizedAssets;
        return $this;
    }
}
