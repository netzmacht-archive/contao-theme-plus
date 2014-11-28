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

use Assetic\Asset\AssetInterface;
use Symfony\Component\EventDispatcher\Event;

class GenerateAssetPathEvent extends Event
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
     * @var AssetInterface
     */
    protected $asset;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $path;

    public function __construct(\PageModel $page, \LayoutModel $layout, AssetInterface $asset, $type)
    {
        $this->page   = $page;
        $this->layout = $layout;
        $this->asset  = $asset;
        $this->type   = (string) $type;
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
     * @return AssetInterface
     */
    public function getAsset()
    {
        return $this->asset;
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
