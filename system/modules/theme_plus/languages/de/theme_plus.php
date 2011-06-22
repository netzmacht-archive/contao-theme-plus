<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Back end modules
 */
$GLOBALS['TL_LANG']['theme_plus']['upgrade1.5']   = 'Auto-Upgrade <strong>theme_plus</strong> auf Version <strong>1.5</strong> abgeschlossen!';
$GLOBALS['TL_LANG']['theme_plus']['upgrade2.0']   = 'Ihre <strong>layout_additional_sources</strong> Einstellungen wurden auf <strong>theme_plus</strong> portiert, <strong>layout_additional_sources</strong> wurde von Ihrem System entfernt!<br/>Denken Sie daran, ein <a href="contao/main.php?do=repository_manager&update=database">Datenbankupdate</a> zu machen!';
$GLOBALS['TL_LANG']['theme_plus']['cssMinimizer'] = array(
	'Der YUI Compressor ist auf Ihrem System nicht verfügbar.<br/>
Alternativ können Sie den <strong>cssMinimizer</strong> benutzen um CSS Dateien zu minimieren.',
	'cssMinimizer installieren',
	'Handbuch zum Einrichten des YUI Compressor',
	'Der <strong>cssMinimizer</strong> wurde installiert, wird aber nicht zur Minimierung der CSS Dateien verwendet!',
	'cssMinimizer aktivieren');
$GLOBALS['TL_LANG']['theme_plus']['jsMinimizer']        = array(
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