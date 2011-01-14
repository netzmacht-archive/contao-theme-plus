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
$GLOBALS['TL_LANG']['layout_additional_sources']['upgrade1.5']         = 'Auto-Upgrade <strong>layout_additional_sources</strong> to version <strong>1.5.0 stable</strong> completed!';
$GLOBALS['TL_LANG']['layout_additional_sources']['cssMinimizer']       = array(
	'Der YUI Compressor ist auf Ihrem System nicht verfügbar. Alternativ können Sie den <strong>cssMinimizer</strong> benutzen um CSS Dateien zu minimieren. <strong>Nach der Installation muss in den Systemeinstellungen die CSS Komprimierung auf <u>cssMinimizer</u> gestellt werden!</strong>',
	'cssMinimizer installieren');
$GLOBALS['TL_LANG']['layout_additional_sources']['jsMinimizer']        = array(
	'Der YUI Compressor ist auf Ihrem System nicht verfügbar. Alternativ können Sie den <strong>Dean Edwards Packer</strong> oder <strong>jsMinimizer</strong> benutzen um JS Dateien zu minimieren. <strong>Nach der Installation muss in den Systemeinstellungen die JS Komprimierung auf <u>Dean Edwards Packer</u>/<u>jsMinimizer</u> gestellt werden!</strong>',
	'Dean Edwards Packer installieren',
	'jsMinimizer installieren');

?>