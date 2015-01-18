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
 * Class FilterRules
 */
class FilterRules implements \Countable, \IteratorAggregate, \Serializable
{
    /**
     * @var array|FilterRule[]
     */
    private $rules = [];

    /**
     * Add a filter rule.
     *
     * This will only add the rule, if it is not already part of this set.
     *
     * @param FilterRule $filterRule
     *
     * @return $this
     */
    public function add(FilterRule $filterRule)
    {
        if (!$this->contains($filterRule)) {
            $this->rules[] = $filterRule;
        }
        return $this;
    }

    /**
     * Add multiple filter rules.
     *
     * This will only add filter rules, that are not already part of this set.
     *
     * @param FilterRules|array|FilterRule[] $filterRules
     *
     * @return $this
     */
    public function addAll($filterRules)
    {
        foreach ($filterRules as $filterRule) {
            if (!$this->contains($filterRule)) {
                $this->rules[] = $filterRule;
            }
        }
        return $this;
    }

    public function all()
    {
        return $this->rules;
    }

    /**
     * Determine if a filter rule exist in this rule set.
     *
     * @param FilterRule $filterRule
     *
     * @return bool
     */
    public function contains(FilterRule $filterRule)
    {
        foreach ($this->rules as $rule) {
            if ($rule == $filterRule) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if all filter rules are existing in this rule set.
     *
     * @param FilterRules|array|FilterRule[] $filterRules
     *
     * @return bool
     */
    public function containsAll($filterRules)
    {
        foreach ($filterRules as $filterRule) {
            if (!$this->contains($filterRule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if this set is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->rules);
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
