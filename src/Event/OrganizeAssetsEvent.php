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
use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use Symfony\Component\EventDispatcher\Event;

class OrganizeAssetsEvent extends Event
{

    /**
     * @var \PageModel
     */
    protected $page;

    /**
     * @var \LayoutModel
     */
    protected $layout;

    /**
     * @var AssetCollectionInterface
     */
    protected $assets;

    /**
     * @var AssetCollectionInterface|null
     */
    protected $organizedAssets;

    public function __construct(
        \PageModel $page,
        \LayoutModel $layout,
        $defaultFilters,
        AssetCollectionInterface $assets,
        DeveloperTool $developerTool = null
    ) {
        $this->page           = $page;
        $this->layout         = $layout;
        $this->defaultFilters = $defaultFilters;
        $this->assets         = $assets;
        $this->developerTool  = $developerTool;
    }

    /**
     * @return \PageModel
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return \LayoutModel
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return \Assetic\Filter\FilterInterface[]|null
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
        return $this->assets;
    }

    /**
     * @return DeveloperTool|null
     */
    public function getDeveloperTool()
    {
        return $this->developerTool;
    }

    /**
     * @return AssetCollectionInterface|null
     */
    public function getOrganizedAssets()
    {
        return $this->organizedAssets;
    }

    /**
     * @param AssetCollectionInterface|null $organizedAssets
     *
     * @return static
     */
    public function setOrganizedAssets(AssetCollectionInterface $organizedAssets = null)
    {
        $this->organizedAssets = $organizedAssets;
        return $this;
    }
}
