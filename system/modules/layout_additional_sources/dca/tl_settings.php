<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{additional_source_legend:hide},yui_cmd,yui_compression_disabled,gz_compression_disabled';
$GLOBALS['TL_DCA']['tl_settings']['fields']['yui_cmd'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['yui_cmd'],
	'default'                 => 'yui-compressor',
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'long')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['yui_compression_disabled'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['yui_compression_disabled'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['gz_compression_disabled'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['gz_compression_disabled'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

?>