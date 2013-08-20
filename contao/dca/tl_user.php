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


/**
 * Table tl_user
 */
MetaPalettes::appendAfter('tl_user', 'login', 'theme', array('frontend' => array(':hide', 'themePlusDesignerMode')));
MetaPalettes::appendAfter('tl_user', 'admin', 'theme', array('frontend' => array(':hide', 'themePlusDesignerMode')));
MetaPalettes::appendAfter('tl_user', 'default', 'theme', array('frontend' => array(':hide', 'themePlusDesignerMode')));
MetaPalettes::appendAfter('tl_user', 'group', 'theme', array('frontend' => array(':hide', 'themePlusDesignerMode')));
MetaPalettes::appendAfter('tl_user', 'extend', 'theme', array('frontend' => array(':hide', 'themePlusDesignerMode')));
MetaPalettes::appendAfter('tl_user', 'custom', 'theme', array('frontend' => array(':hide', 'themePlusDesignerMode')));

$GLOBALS['TL_DCA']['tl_user']['fields']['themePlusDesignerMode'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['themePlusDesignerMode'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_stylesheet';
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_javascript';
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_variable';
