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

class GenerateAssetPathEvent extends AssetAwareEvent
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $path;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        $asset,
        FilterCollection $defaultFilters = null,
        $type
    ) {
        parent::__construct($renderMode, $page, $layout, $asset, $defaultFilters);
        $this->type = (string) $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public function setPath($path)
    {
        $this->path = (string) $path;
        return $this;
    }
}
