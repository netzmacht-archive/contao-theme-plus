<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{additional_source_legend:hide},additional_sources_combination,additional_sources_css_compression,additional_sources_js_compression,additional_sources_gz_compression_disabled,additional_sources_hide_cssmin_message,additional_sources_hide_jsmin_message';
$GLOBALS['TL_DCA']['tl_settings']['fields']['additional_sources_combination'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_combination'],
	'default'                 => 'combine_all',
	'inputType'               => 'select',
	'options'                 => array('combine_all', 'combine_local', 'combine_none'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_combinations'],
	'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'long')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['additional_sources_css_compression'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_css_compression'],
	'default'                 => 'combine_all',
	'inputType'               => 'select',
	'options_callback'        => array('tl_settings_layout_additional_sources', 'getCssMinimizers'),
	'eval'                    => array('decodeEntities'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['additional_sources_js_compression'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_js_compression'],
	'default'                 => 'combine_all',
	'inputType'               => 'select',
	'options_callback'        => array('tl_settings_layout_additional_sources', 'getJsMinimizers'),
	'eval'                    => array('decodeEntities'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['additional_sources_gz_compression_disabled'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_gz_compression_disabled'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['additional_sources_hide_cssmin_message'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_hide_cssmin_message'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr w50 m12')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['additional_sources_hide_jsmin_message'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['additional_sources_hide_jsmin_message'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

class tl_settings_layout_additional_sources extends Backend
{
	public function __construct()
	{
		$this->import('Compression');
		$this->import('Config');
	}
	
	
	public function getCssMinimizers()
	{
		$arrMinimizers = array_merge
		(
			array('' => $GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['inherit']),
			$this->Compression->getCssMinimizers()
		);
		
		if (in_array('lesscss', $this->Config->getActiveModules()))
		{
			$arrMinimizers['less.js'] = $GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['less.js'];
			if ($this->tryNode())
			{
				$arrMinimizers['less.js+pre'] = $GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['less.js+pre'];
			}
			foreach ($this->Compression->getCssMinimizers() as $strKey=>$strValue)
			{
				if ($strKey != 'none')
				{
					$arrMinimizers['less.js+' . $strKey] = $GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['less.js'] . ' + ' . $strValue;
				}
			}
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
	
	
	public function getJsMinimizers()
	{
		$arrMinimizers = array_merge
		(
			array('' => $GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['inherit']),
			$this->Compression->getJsMinimizers()
		);
		return $arrMinimizers;
	}
}

?>