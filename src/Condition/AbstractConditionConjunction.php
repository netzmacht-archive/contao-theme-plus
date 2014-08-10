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