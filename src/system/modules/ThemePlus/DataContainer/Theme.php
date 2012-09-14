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
 * Class Theme
 */
class Theme extends \Backend
{
    protected function __construct()
    {
        parent::__construct();

        $this->import('BackendUser', 'User');
    }

    public function editStylesheet($row, $href, $label, $title, $icon, $attributes)
	{
		if ($this->User->isAdmin || $this->User->hasAccess('theme_plus_stylesheet', 'themes')) {
            return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
		}
        return $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
	}

	public function editJavaScript($row, $href, $label, $title, $icon, $attributes)
	{
		if ($this->User->isAdmin || $this->User->hasAccess('theme_plus_javascript', 'themes')) {
            return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
		}
        return $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
	}

	public function editVariable($row, $href, $label, $title, $icon, $attributes)
	{
		if ($this->User->isAdmin || $this->User->hasAccess('theme_plus_variable', 'themes')) {
            return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
		}
        return $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
	}
}