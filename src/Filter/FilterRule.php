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

namespace Bit3\Contao\ThemePlus\Filter;

/**
 * Class FilterRule
 */
class FilterRule implements \Serializable
{
    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $system;

    /**
     * @var string
     */
    private $browser;

    /**
     * @var string
     */
    private $comparator;

    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $invert;

    public function __construct($platform, $system, $browser, $comparator, $version, $invert)
    {
        $this->platform   = (string) $platform;
        $this->system     = (string) $system;
        $this->browser    = (string) $browser;
        $this->comparator = (string) $comparator;
        $this->version    = (string) $version;
        $this->invert     = (bool) $invert;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @return string
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @return string
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return boolean
     */
    public function isInvert()
    {
        return $this->invert;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            [
                $this->platform,
                $this->system,
                $this->browser,
                $this->comparator,
                $this->version
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->platform,
            $this->system,
            $this->browser,
            $this->comparator,
            $this->version
            ) = unserialize($serialized);
    }
}
