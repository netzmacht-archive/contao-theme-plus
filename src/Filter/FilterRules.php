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

use Traversable;

/**
 * Class FilterRules
 */
class FilterRules implements \Countable, \IteratorAggregate, \Serializable
{
    /**
     * @var array|FilterRule[]
     */
    private $rules = [];

    public function add(FilterRule $filterRule)
    {
        $this->rules[] = $filterRule;
    }

    public function all()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->rules = unserialize($serialized);
    }
}
