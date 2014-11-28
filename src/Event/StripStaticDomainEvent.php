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

use Symfony\Component\EventDispatcher\Event;

class StripStaticDomainEvent extends Event
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
     * @var string
     */
    protected $url;

    public function __construct(\PageModel $page, \LayoutModel $layout, $url)
    {
        $this->page   = $page;
        $this->layout = $layout;
        $this->url    = (string) $url;
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return static
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;
        return $this;
    }
}
