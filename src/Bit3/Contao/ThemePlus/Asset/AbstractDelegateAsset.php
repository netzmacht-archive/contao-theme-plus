<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\ThemePlus\Asset;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Class AbstractDelegateAsset
 */
class AbstractDelegateAsset implements AssetInterface
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