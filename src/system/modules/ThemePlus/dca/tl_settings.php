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


/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{theme_plus_legend:hide},theme_plus_lesscss_mode,theme_plus_force_less,css_embed_images';

$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_lesscss_mode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_lesscss_mode'],
	'default'                 => 'less.js',
	'inputType'               => 'select',
	'options_callback'        => array('tl_settings_theme_plus', 'getLessCssModes'),
	'eval'                    => array('decodeEntities'=> true,
	                                   'tl_class'      => 'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_force_less']   = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_force_less'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=> 'm12 w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['css_embed_images']        = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['css_embed_images'],
	'inputType'               => 'select',
	'options'                 => array
	(
		0       => '-',
		1024    => '1 KiB',
		2048    => '2 KiB',
		4069    => '4 KiB',
		8192    => '8 KiB',
		16384   => '16 KiB',
		32768   => '32 KiB',
		65536   => '64 KiB',
		131072  => '128 KiB',
		262144  => '256 KiB',
		524288  => '512 KiB',
		1048576 => '1 MiB'
	),
	'eval'                    => array('tl_class'=> 'w50')
);

class tl_settings_theme_plus extends Backend
{
	public function __construct()
	{
		$this->import('Compression');
		$this->import('Config');
	}


	public function getLessCssModes()
	{
		$arrMinimizers = array();

		if (in_array('lesscss', $this->Config->getActiveModules())) {
			// add javascript less support
			$arrMinimizers['less.js'] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['less.js'];

			if ($this->tryNode()) {
				// add precompiled less support
				$arrMinimizers['less.js+pre'] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['less.js+pre'];
			}
		}

		if (in_array('phpless', $this->Config->getActiveModules())) {
			// add php less support
			$arrMinimizers['phpless'] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['phpless'];
		}

		if (!count($arrMinimizers)) {
			$arrMinimizers['-'] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['noless'];
		}

		return $arrMinimizers;
	}


	protected function tryNode()
	{
		// execute lessc
		$procLessC = proc_open(
			'node --version',
			array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w")
			),
			$arrPipes);
		if ($procLessC === false) {
			return false;
		}
		// close stdin
		fclose($arrPipes[0]);
		// close stdout
		fclose($arrPipes[1]);
		// read and close stderr
		$strErr = stream_get_contents($arrPipes[2]);
		fclose($arrPipes[2]);
		// wait until yui-compressor terminates
		$intCode = proc_close($procLessC);
		if ($intCode != 0 || strlen($strErr)) {
			return false;
		}
		return true;
	}
}
