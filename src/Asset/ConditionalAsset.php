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
 * Class ConditionalAsset
 */
class ConditionalAsset
   extends AbstractDelegatorAsset
{

    /**
     * @var ConditionInterface
     */
    protected $conditions;

    /**
     * Create new conditional asset.
     *
     * @param AssetInterface     $asset      The delegate asset.
     * @param ConditionInterface $conditions The condition.
     */
    public function __construct(AssetInterface $asset, ConditionInterface $conditions)
    {
        parent::__construct($asset);
        $this->conditions = $conditions;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(FilterInterface $additionalFilter = null)
    {
        if ($this->conditions->accept()) {
            return parent::dump($additionalFilter);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if ($this->conditions->accept()) {
            return parent::getContent();
        }

        return '';
    }
}
