<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2012 InfinitySoft <http://www.infinitysoft.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace InfinitySoft\ThemePlus\DataContainer;

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
			$this->log('Not enough permissions to access the Theme+ javascript module', 'tl_theme_plus_javascript checkPermission', TL_ERROR);
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
}