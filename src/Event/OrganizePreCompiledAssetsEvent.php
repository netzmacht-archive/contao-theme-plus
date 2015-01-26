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
use Assetic\Filter\FilterCollection;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetCollection;

class OrganizePreCompiledAssetsEvent extends AssetCollectionAwareEvent
{
    /**
     * @var \ArrayObject|ExtendedAssetCollection[]
     */
    protected $collections;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        AssetCollectionInterface $collection,
        FilterCollection $defaultFilters = null
    ) {
        parent::__construct($renderMode, $page, $layout, $collection, $defaultFilters);
        $this->collections = new \ArrayObject();
    }

    /**
     * @return \ArrayObject|ExtendedAssetCollection[]
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * @param \ArrayObject|ExtendedAssetCollection[] $collections
     *
     * @return static
     */
    public function setCollections(\ArrayObject $collections)
    {
        $this->collections = $collections;
        return $this;
    }
}
