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

use Bit3\Contao\ThemePlus\RenderMode;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent
{
    /**
     * The rendering mode.
     *
     * @var string
     */
    protected $renderMode;

    public function __construct($renderMode)
    {
        $this->renderMode = (string) $renderMode;
    }

    /**
     * @return string
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }

    /**
     * Determine if live mode is enabled.
     *
     * @return bool
     */
    public function isLiveMode()
    {
        return RenderMode::LIVE == $this->renderMode;
    }

    /**
     * Determine if designer mode is enabled.
     *
     * @return bool
     */
    public function isDesignerMode()
    {
        return RenderMode::DESIGN == $this->renderMode;
    }

    /**
     * Determine if pre-compile mode is enabled.
     *
     * @return bool
     */
    public function isInPreCompileMode()
    {
        return RenderMode::PRE_COMPILE == $this->renderMode;
    }
}
