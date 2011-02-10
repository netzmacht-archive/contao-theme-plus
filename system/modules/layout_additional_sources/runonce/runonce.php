<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Layout Additional Sources
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
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
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    LGPL
 * @filesource
 */


/**
 * Class LayoutAdditionalSourcesRunonce
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LayoutAdditionalSourcesRunonce extends Frontend
{

	/**
	 * Initialize the object
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->import('Database');
	}
	
	
	/**
	 * Update a configuration entry.
	 * @param string $strKey
	 */
	protected function updateConfig($strKey, $strValue)
	{
		$GLOBALS['TL_CONFIG'][$strKey] = $strValue;
		$strKey = sprintf("\$GLOBALS['TL_CONFIG']['%s']", $strKey);
		$this->Config->update($strKey, $strValue);
	}
	
	
	/**
	 * Delete a configuration entry.
	 * @param string $strKey
	 */
	protected function deleteConfig($strKey)
	{
		$strKey = sprintf("\$GLOBALS['TL_CONFIG']['%s']", $strKey);
		$this->Config->delete($strKey);
	}
	
	
	/**
	 * Test if yui compressor exists.
	 */
	protected function testYUI()
	{
		$strCmd = escapeshellcmd($GLOBALS['TL_CONFIG']['additional_sources_yui_cmd']);
		$proc = proc_open(
			$strCmd,
			array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w")
			),
			$arrPipes);
		if ($proc === false)
		{
			return false;
		}
		// close stdin
		fclose($arrPipes[0]);
		// read and close stdout
		$strOut = stream_get_contents($arrPipes[1]);
		fclose($arrPipes[1]);
		// read and close stderr
		$strErr = stream_get_contents($arrPipes[2]);
		fclose($arrPipes[2]);
		// wait until process terminates
		proc_close($proc);
		
		// no error means, the command was found and successfully executed
		return !strlen($strErr);
	}

	public function run()
	{
		$this->upgrade1_5();
		$this->upgrade1_6();
		$this->checkCompression();
	}
	
	
	/**
	 * Database upgrade to 1.5
	 */
	protected function upgrade1_5()
	{
		if ($this->Database->tableExists('tl_additional_source') && !$this->Database->fieldExists('additional_source', 'tl_layout'))
		{
			$this->Database->execute('ALTER TABLE tl_layout ADD additional_source blob NULL');
			
			// go over all themes
			$objTheme = $this->Database->execute("SELECT * FROM tl_theme");
			while ($objTheme->next())
			{
				// list all additional sources
				$objAdditionalSource = $this->Database->prepare("
						SELECT * FROM tl_additional_source WHERE pid=?")
					->execute($objTheme->id);
				
				// go over all theme layouts
				$objLayout = $this->Database->prepare("
						SELECT * FROM tl_layout WHERE pid=?")
					->execute($objTheme->id);
				while ($objLayout->next())
				{
					$arrAdditionalSource = array();
					$objAdditionalSource->reset();
					while ($objAdditionalSource->next())
					{
						if ($objAdditionalSource->restrictLayout)
						{
							$arrLayouts = deserialize($objAdditionalSource->layout);
							if (!in_array($objLayout->id, $arrLayouts))
							{
								continue;
							}
						}
						
						if ($objAdditionalSource->editor_only)
						{
							continue;
						}
						
						$arrAdditionalSource[] = $objAdditionalSource->id;
					}
					
					$this->Database->prepare("
							UPDATE tl_layout SET additional_source=? WHERE id=?")
						->execute(serialize($arrAdditionalSource), $objLayout->id);
				}
			}
			
			$this->loadLanguageFile('layout_additional_sources');
			$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['layout_additional_sources']['upgrade1.5'];
		}
	}
	
	
	/**
	 * Configuration upgrade to 1.6
	 * Enter description here ...
	 */
	protected function upgrade1_6()
	{
		/**
		 * Convert old setting.
		 */
		if (isset($GLOBALS['TL_CONFIG']['yui_compression_disabled']))
		{
			if ($GLOBALS['TL_CONFIG']['yui_compression_disabled'])
			{
				$this->updateConfig('additional_sources_css_compression', 'none');
				$this->updateConfig('additional_sources_js_compression', 'none');
			}
			$this->deleteConfig('yui_compression_disabled');
		}
		if (isset($GLOBALS['TL_CONFIG']['yui_cmd']))
		{
			$this->updateConfig('additional_sources_yui_cmd', $GLOBALS['TL_CONFIG']['yui_cmd']);
			$this->deleteConfig('yui_cmd');
		}
		if (isset($GLOBALS['TL_CONFIG']['gz_compression_disabled']))
		{
			$this->updateConfig('additional_sources_gz_compression_disabled', $GLOBALS['TL_CONFIG']['gz_compression_disabled']);
			$this->deleteConfig('gz_compression_disabled');
		}
	}
	
	
	/**
	 * Check the available compression methods.
	 */
	protected function checkCompression()
	{
		/**
		 * Test if yui is available, otherwise disable css compression.
		 */
		if (	$GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'yui'
			||	$GLOBALS['TL_CONFIG']['additional_sources_js_compression']  == 'yui')
		{
			if (!$this->testYUI())
			{
				if ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'yui')
				{
					// try cssmin compression instead
					$this->updateConfig('additional_sources_css_compression', 'cssmin');
				}
				if ($GLOBALS['TL_CONFIG']['additional_sources_js_compression']  == 'yui')
				{
					$this->updateConfig('additional_sources_js_compression', 'jsmin');
				}
			}
		}
		
		/**
		 * Test if cssmin is available, otherwise disable css compression.
		 */
		if (	$GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'cssmin')
		{
			if (!file_exists(TL_ROOT . '/system/libraries/CssMinimizer.php'))
			{
				$this->updateConfig('additional_sources_css_compression', 'none');
			}
		}
		
		/**
		 * Test if jsmin is available, otherwise try dep.
		 */
		if (	$GLOBALS['TL_CONFIG']['additional_sources_js_compression'] == 'jsmin')
		{
			if (!file_exists(TL_ROOT . '/system/libraries/JsMinimizer.php'))
			{
				$this->updateConfig('additional_sources_js_compression', 'dep');
			}
		}
		
		/**
		 * Test if dep is available, otherwise disable js compression.
		 */
		if (	$GLOBALS['TL_CONFIG']['additional_sources_js_compression'] == 'dep')
		{
			if (!file_exists(TL_ROOT . '/system/libraries/DeanEdwardsPacker.php'))
			{
				$this->updateConfig('additional_sources_js_compression', 'none');
			}
		}
	}
}

/**
 * Instantiate controller
 */
$objLayoutAdditionalSourcesRunonce = new LayoutAdditionalSourcesRunonce();
$objLayoutAdditionalSourcesRunonce->run();

?>