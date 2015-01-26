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
use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/**
 * Class AssetCollectionAwareEvent
 */
class AssetCollectionAwareEvent extends LayoutAwareEvent
{
    /**
     * @var AssetCollectionInterface|AssetInterface[]
     */
    protected $collection;

    /**
     * @var FilterCollection|FilterInterface[]|null
     */
    protected $defaultFilters = null;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        AssetCollectionInterface $collection,
        FilterCollection $defaultFilters
    ) {
        parent::__construct($renderMode, $page, $layout);
        $this->collection     = $collection;
        $this->defaultFilters = $defaultFilters;
    }

    /**
     * @return AssetCollectionInterface|AssetInterface[]
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return FilterCollection|FilterInterface[]|null
     */
    public function getDefaultFilters()
    {
        return $this->defaultFilters;
    }
}
