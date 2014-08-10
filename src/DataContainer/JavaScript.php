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

namespace Bit3\Contao\ThemePlus\DataContainer;

/**
 * Class JavaScript
 */
class JavaScript extends File
{
	/**
	 * Check permissions to edit the table
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin) {
			return;
		}

		if (!$this->User->hasAccess('theme_plus_javascript', 'themes')) {
			$this->log(
				'Not enough permissions to access the Theme+ javascript module',
				'tl_theme_plus_javascript checkPermission',
				TL_ERROR
			);
			$this->redirect('contao/main.php?act=error');
		}
	}

	public function rememberType($varValue)
	{
		\Session::getInstance()->set('THEME_PLUS_LAST_JS_TYPE', $varValue);

		return $varValue;
	}

	public function getAsseticFilterOptions()
	{
		return $this->buildAsseticFilterOptions('js');
	}

	public function loadLayouts($value, $dc)
	{
		return $this->loadLayoutsFor('theme_plus_javascripts', $dc);
	}

	public function saveLayouts($value, $dc)
	{
		return $this->saveLayoutsFor('theme_plus_javascripts', $value, $dc);
	}
}