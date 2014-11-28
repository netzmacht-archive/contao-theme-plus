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
 * Class ConditionalAsset
 */
class ConditionalAsset extends AbstractDelegatorAsset
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
