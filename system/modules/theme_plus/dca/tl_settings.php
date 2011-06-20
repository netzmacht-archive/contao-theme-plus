<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{theme_plus_legend:hide},theme_plus_combination,theme_plus_lesscss_mode,theme_plus_gz_compression_disabled,theme_plus_hide_cssmin_message,theme_plus_hide_jsmin_message';

$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_aggregate_externals'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_aggregate_externals'],
	'inputType'               => 'checkbox',
	'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_lesscss_mode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_lesscss_mode'],
	'default'                 => 'less.js',
	'inputType'               => 'select',
	'options_callback'        => array('tl_settings_theme_plus', 'getLessCssModes'),
	'eval'                    => array('decodeEntities'=>true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_gz_compression_disabled'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_gz_compression_disabled'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
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
		
		if (in_array('lesscss', $this->Config->getActiveModules()))
		{
			$arrMinimizers['less.js'] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['less.js'];
			if ($this->tryNode())
			{
				$arrMinimizers['less.js+pre'] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['less.js+pre'];
			}
			foreach ($this->Compression->getCssMinimizers() as $strKey=>$strValue)
			{
				if ($strKey != 'none')
				{
					$arrMinimizers['less.js+' . $strKey] = $GLOBALS['TL_LANG']['tl_settings']['theme_plus_compression']['less.js+pre'] . ' + ' . $strValue;
				}
			}
		}
		else
		{
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
		if ($procLessC === false)
		{
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
		if ($intCode != 0 || strlen($strErr))
		{
			return false;
		}
		return true;
	}
}

?>