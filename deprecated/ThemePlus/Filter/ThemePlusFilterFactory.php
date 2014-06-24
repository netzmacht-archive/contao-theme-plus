<?php

/**
 * Assetic for Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG
 *
 * @package Assetic
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

trigger_error('Deprecated, use Bit3\Contao\ThemePlus\Filter\ThemePlusFilterFactory instead', E_USER_DEPRECATED);
class_alias('Bit3\Contao\ThemePlus\Filter\ThemePlusFilterFactory', 'ThemePlus\Filter\ThemePlusFilterFactory');
