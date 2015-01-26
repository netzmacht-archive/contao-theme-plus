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

use Bit3\Contao\ThemePlus\Filter\FilterRules;

trait ExtendedAssetTrait
{
    /**
     * The conditional comment.
     *
     * @var string
     */
    protected $conditionalComment;

    /**
     * The media query.
     *
     * @var string
     */
    protected $mediaQuery;

    /**
     * Embed inline.
     *
     * @var bool
     */
    protected $inline = false;

    /**
     * Do not combine.
     *
     * @var bool
     */
    protected $standalone = false;

    /**
     * The filter rules.
     *
     * @var FilterRules|null
     */
    protected $filterRules = null;

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::getConditionalComment()
     */
    public function getConditionalComment()
    {
        return $this->conditionalComment;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::setConditionalComment()
     */
    public function setConditionalComment($conditionalComment)
    {
        $this->conditionalComment = (string) $conditionalComment;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::getMediaQuery()
     */
    public function getMediaQuery()
    {
        return $this->mediaQuery;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::setMediaQuery()
     */
    public function setMediaQuery($mediaQuery)
    {
        $this->mediaQuery = (string) $mediaQuery;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::isInline()
     */
    public function isInline()
    {
        return $this->inline;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::setInline()
     */
    public function setInline($inline)
    {
        $this->inline = (bool) $inline;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::isStandalone()
     */
    public function isStandalone()
    {
        return $this->standalone;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::setStandalone()
     */
    public function setStandalone($standalone)
    {
        $this->standalone = (bool) $standalone;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::getFilterRules()
     */
    public function getFilterRules()
    {
        return $this->filterRules;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExtendedAssetInterface::setFilterRules()
     */
    public function setFilterRules(FilterRules $filterRules = null)
    {
        $this->filterRules = $filterRules;
        return $this;
    }
}