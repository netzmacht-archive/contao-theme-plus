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

namespace Bit3\Contao\ThemePlus\Condition;

abstract class AbstractConditionConjunction implements ConditionInterface
{

    /**
     * @var ConditionInterface[]
     */
    protected $conditions = [];

    /**
     * @return ConditionInterface[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param ConditionInterface[] $conditions
     *
     * @return static
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = [];
        $this->addConditions($conditions);
        return $this;
    }

    /**
     * @param ConditionInterface[] $conditions
     *
     * @return static
     */
    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }
        return $this;
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return static
     */
    public function addCondition(ConditionInterface $condition)
    {
        $hash                    = spl_object_hash($condition);
        $this->conditions[$hash] = $condition;
        return $this;
    }

    /**
     * @param ConditionInterface[] $conditions
     *
     * @return static
     */
    public function removeConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $this->removeCondition($condition);
        }
        return $this;
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return static
     */
    public function removeCondition(ConditionInterface $condition)
    {
        $hash = spl_object_hash($condition);
        unset($this->conditions[$hash]);
        return $this;
    }
}