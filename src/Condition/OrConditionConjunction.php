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

class OrConditionConjunction extends AbstractConditionConjunction
{

	/**
	 * {@inheritdoc}
	 */
	public function accept()
	{
		if (empty($this->conditions)) {
			return true;
		}

		foreach ($this->conditions as $condition) {
			if ($condition->accept()) {
				return true;
			}
		}

		return false;
	}

}