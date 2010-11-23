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
 * Class LayoutAdditionalSources
 * 
 * Adding additional sources to the page layout.
 * 
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LayoutAdditionalSources extends Frontend {
	public function __construct() {
		$this->import('Database');
	}
	
	public function generatePage(Database_Result $objPage, Database_Result $objLayout, PageRegular $objPageRegular) {
		$objAdditionalSources = $this->Database->prepare("SELECT * FROM tl_additional_source WHERE pid=? ORDER BY sorting")
											   ->execute($objLayout->pid);
		while ($objAdditionalSources->next()) {
			// If the source is restricted ...
			if ($objAdditionalSources->restrictLayout) {
				$arrLayouts = unserialize($objAdditionalSources->layout);
				// ... check the layout
				if (!in_array($objLayout->id, $arrLayouts)) {
					continue;
				}
			}
			
			// the variable that store the html code
			$strAdditionalSource = '';
			
			// type of the source
			$strType = $objAdditionalSources->type;
			// uri of the source
			$strSrc = $objAdditionalSources->$strType;
			
			if (	$GLOBALS['TL_CONFIG']['additional_sources_compression'] == 'always'
				||  $GLOBALS['TL_CONFIG']['additional_sources_compression'] == 'no_be_user'
				&&  !BE_USER_LOGGED_IN)
			{
				// yui compression
				if (	$objAdditionalSources->compress_yui
					&&  (	$strType == 'js_file'
						||  $strType == 'css_file'))
				{
					$strTarget = preg_replace('#\.(js|css)$#', '.yui.$1', $strSrc);
					if (strlen($objAdditionalSources->compress_outdir)) {
						$strTarget = $objAdditionalSources->compress_outdir . '/' . basename($strTarget);
					}
					if (	!file_exists($strTarget)
						||  filemtime($strTarget) < filemtime($strSrc))
					{
						$strCmd = sprintf("%s -o %s %s",
							escapeshellcmd($GLOBALS['TL_CONFIG']['yui_cmd']),
							escapeshellarg(TL_ROOT . '/' . $strTarget),
							escapeshellarg(TL_ROOT . '/' . $strSrc));
						// execute yui-compressor
						$procYUI = proc_open(
							$strCmd,
							array(
								0 => array("pipe", "r"),
								1 => array("pipe", "w"),
								2 => array("pipe", "w")
							),
							$arrPipes);
						if ($procYUI === false) {
							throw new Exception(sprintf("yui compressor could not be started!"));
						}
						// close stdin
						fclose($arrPipes[0]);
						// read and close stdout
						$strOut = stream_get_contents($arrPipes[1]);
						fclose($arrPipes[1]);
						// read and close stderr
						$strErr = stream_get_contents($arrPipes[2]);
						fclose($arrPipes[2]);
						// wait until yui-compressor terminates
						$intCode = proc_close($procYUI);
						if ($intCode != 0) {
							throw new Exception(sprintf("Execution of yui compressor failed!\nstdout: %s\nstderr: %s", $strOut, $strErr));
						}
					}
					$strSrc = $strTarget;
				}
				
				// gz compression
				if ($objAdditionalSources->compress_gz)
				{
					$strTarget = $strSrc . '.gz';
					if (strlen($objAdditionalSources->compress_outdir)) {
						$strTarget = $objAdditionalSources->compress_outdir . '/' . basename($strTarget);
					}
					if (	!file_exists($strTarget)
						||  filemtime($strTarget) < filemtime($strSrc))
					{
						$fileSrc = new File($strSrc);
						$fileTarget = new File($strTarget);
						if (!$fileTarget->write(gzencode($fileSrc->getContent()))) {
							throw new Exception(sprintf("GZ Compression of %s to %s failed!", $strTarget));
						}
						unset($fileSrc, $fileTarget);
					}
					$strSrc = $strTarget;
				}
			}
			
			// add the html
			switch ($strType) {
			// js file
			case 'js_file':
			case 'js_url':
				$strAdditionalSource = '<script type="text/javascript" src="'.$strSrc.'"></script>';
				break;
				
			// css file
			case 'css_file':
			case 'css_url':
				$media = unserialize($objAdditionalSources->media);
				if (is_array($media) && count($media)) {
					$media = ' media="'.implode(',', $media).'"';
				} else {
					$media = '';
				}
				$strAdditionalSource = '<link type="text/css" rel="stylesheet" href="'.$strSrc.'"'.$media.' />';
				break;
				
			// for all other, call the hooks
			default:
				if (isset($GLOBALS['TL_HOOKS']['generateAdditionalSource']) && is_array($GLOBALS['TL_HOOKS']['generateAdditionalSource']))
				{
					foreach ($GLOBALS['TL_HOOKS']['generateAdditionalSource'] as $callback)
					{
						$this->import($callback[0]);
						$strAdditionalSource = $this->$callback[0]->$callback[1]($objAdditionalSources, $objPage, $objLayout, $objPageRegular);
						if ($strAdditionalSource !== false || $strAdditionalSource === true)
						{
							break;
						}
					}
				}
			}
			
			// continue, if there is no html
			if (!is_string($strAdditionalSource) || !strlen($strAdditionalSource)) {
				continue;
			}
			
			// add the conditional comment
			if (strlen($objAdditionalSources->cc)) {
				$strAdditionalSource = '<!--[' . $objAdditionalSources->cc . ']>' . $strAdditionalSource . '<![endif]-->';
			}
		
			// add the html to the layout head
			$GLOBALS['TL_HEAD'][] = $strAdditionalSource;
		}
	}
}

?>