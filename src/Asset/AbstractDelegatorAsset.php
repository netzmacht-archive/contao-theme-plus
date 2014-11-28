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

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Class AbstractDelegatorAsset
 */
class AbstractDelegatorAsset implements DelegatorAssetInterface
{
    /**
     * @var AssetInterface
     */
    protected $asset;

    public function __construct(AssetInterface $asset)
    {
        $this->asset = $asset;
    }

    /**
     * @param AssetInterface $asset
     */
    public function setAsset(AssetInterface $asset)
    {
        $this->asset = $asset;
    }

    /**
     * @return AssetInterface
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->asset->ensureFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->asset->getFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function clearFilters()
    {
        return $this->asset->clearFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function load(FilterInterface $additionalFilter = null)
    {
        return $this->asset->load($additionalFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function dump(FilterInterface $additionalFilter = null)
    {
        return $this->asset->dump($additionalFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->asset->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->asset->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceRoot()
    {
        return $this->asset->getSourceRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourcePath()
    {
        return $this->asset->getSourcePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceDirectory()
    {
        return $this->asset->getSourceDirectory();
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath()
    {
        return $this->asset->getTargetPath();
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetPath($targetPath)
    {
        return $this->asset->setTargetPath($targetPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified()
    {
        return $this->asset->getLastModified();
    }

    /**
     * {@inheritdoc}
     */
    public function getVars()
    {
        return $this->asset->getVars();
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values)
    {
        return $this->asset->setValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->asset->getValues();
    }
}