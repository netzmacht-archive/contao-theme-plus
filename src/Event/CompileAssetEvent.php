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

class CompileAssetEvent extends AssetAwareEvent
{
    /**
     * @var bool
     */
    protected $overwrite;

    /**
     * @var string
     */
    protected $targetPath;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout,
        $asset,
        FilterCollection $defaultFilters = null,
        $overwrite = false
    ) {
        parent::__construct($renderMode, $page, $layout, $asset, $defaultFilters);
        $this->overwrite = $overwrite;
    }

    /**
     * @return boolean
     */
    public function isOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * @return string
     */
    public function getTargetPath()
    {
        return $this->targetPath;
    }

    /**
     * @param string $targetPath
     *
     * @return static
     */
    public function setTargetPath($targetPath)
    {
        $this->targetPath = (string) $targetPath;
        return $this;
    }
}
