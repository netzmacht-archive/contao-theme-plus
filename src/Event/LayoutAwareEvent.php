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

/**
 * Class LayoutAwareEvent
 */
class LayoutAwareEvent extends Event
{
    /**
     * @var \PageModel
     */
    protected $page;

    /**
     * @var \LayoutModel
     */
    protected $layout;

    public function __construct(
        $renderMode,
        \PageModel $page,
        \LayoutModel $layout
    ) {
        parent::__construct($renderMode);
        $this->page   = $page;
        $this->layout = $layout;
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
}
