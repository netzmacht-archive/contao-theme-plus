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

namespace Bit3\Contao\ThemePlus\Asset;

trait ExtendedAssetTrait
{
    /**
     * @var string
     */
    protected $conditionalComment;

    /**
     * @var string
     */
    protected $mediaQuery;

    /**
     * @var bool
     */
    protected $inline = false;

    /**
     * @var bool
     */
    protected $standalone = false;

    /**
     * {@inheritdoc}
     */
    public function getConditionalComment()
    {
        return $this->conditionalComment;
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionalComment($conditionalComment)
    {
        $this->conditionalComment = (string) $conditionalComment;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaQuery()
    {
        return $this->mediaQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function setMediaQuery($mediaQuery)
    {
        $this->mediaQuery = (string) $mediaQuery;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInline()
    {
        return $this->inline;
    }

    /**
     * @param bool $inline
     *
     * @return static
     */
    public function setInline($inline)
    {
        $this->inline = (bool) $inline;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isStandalone()
    {
        return $this->standalone;
    }

    /**
     * {@inheritdoc}
     */
    public function setStandalone($standalone)
    {
        $this->standalone = (bool) $standalone;
        return $this;
    }
}