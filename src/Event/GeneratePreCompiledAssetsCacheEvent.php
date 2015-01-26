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

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetCollection;

class GeneratePreCompiledAssetsCacheEvent extends LayoutAwareEvent
{
    /**
     * @var FilterCollection|FilterInterface[]|null
     */
    protected $defaultFilters;

    /**
     * @var \ArrayObject|ExtendedAssetCollection[]
     */
    protected $collections;

    /**
     * @var string
     */
    protected $cacheCode;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        \ArrayObject $collections,
        FilterCollection $defaultFilters = null
    ) {
        parent::__construct($renderMode, $page, $layout);
        $this->collections    = $collections;
        $this->defaultFilters = $defaultFilters;
    }

    /**
     * @return \Assetic\Filter\FilterInterface[]|null
     */
    public function getDefaultFilters()
    {
        return $this->defaultFilters;
    }

    /**
     * @return \ArrayObject|ExtendedAssetCollection[]
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * @return string
     */
    public function getCacheCode()
    {
        return $this->cacheCode;
    }

    /**
     * @param string $cacheCode
     *
     * @return static
     */
    public function setCacheCode($cacheCode)
    {
        $this->cacheCode = (string) $cacheCode;
        return $this;
    }
}
