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

trigger_error('Deprecated, use Bit3\Contao\ThemePlus\DataContainer\Content instead', E_USER_DEPRECATED);
class_alias('Bit3\Contao\ThemePlus\DataContainer\Content', 'ThemePlus\DataContainer\Content');
