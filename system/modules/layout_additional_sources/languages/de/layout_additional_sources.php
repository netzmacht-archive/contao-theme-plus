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
 * Back end modules
 */
$GLOBALS['TL_LANG']['layout_additional_sources']['upgrade1.5']   = 'Auto-Upgrade <strong>layout_additional_sources</strong> auf Version <strong>1.5</strong> abgeschlossen!';
$GLOBALS['TL_LANG']['layout_additional_sources']['cssMinimizer'] = array(
	'Der YUI Compressor ist auf Ihrem System nicht verfügbar.<br/>
Alternativ können Sie den <strong>cssMinimizer</strong> benutzen um CSS Dateien zu minimieren.',
	'cssMinimizer installieren',
	'Handbuch zum Einrichten des YUI Compressor',
	'Der <strong>cssMinimizer</strong> wurde installiert, wird aber nicht zur Minimierung der CSS Dateien verwendet!',
	'cssMinimizer aktivieren');
$GLOBALS['TL_LANG']['layout_additional_sources']['jsMinimizer']        = array(
	'Der YUI Compressor ist auf Ihrem System nicht verfügbar.<br/>
Alternativ können Sie den <strong>jsMinimizer</strong> oder <strong>Dean Edwards Packer</strong> benutzen um JS Dateien zu minimieren.',
	'jsMinimizer installieren',
	'Dean Edwards Packer installieren',
	'Handbuch zum Einrichten des YUI Compressor',
	'Der <strong>jsMinimizer</strong> wurde installiert, wird aber nicht zur Minimierung der JS Dateien verwendet!',
	'jsMinimizer aktivieren',
	'Der <strong>Dean Edwards Packer</strong> wurde installiert, wird aber nicht zur Minimierung der JS Dateien verwendet!',
	'Dean Edwards Packer aktivieren');
?>