<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


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