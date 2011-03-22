<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Layout Additional Sources
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * Class DefaultJsCompiler
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class DefaultJsCompiler extends CompilerBase {
	public function __construct()
	{
		$this->import('Compression');
		
		$strJsMinimizerClass = $this->Compression->getJsMinimizerClass($GLOBALS['TL_CONFIG']['additional_sources_js_compression']);
		$this->import($strJsMinimizerClass, 'JsMinimizer');
		
		$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
		$this->import($strGzipCompressorClass, 'GzipCompressor');
	}
	
	
	public function compile($arrSourcesMap, &$arrSources, $blnUserLoggedIn)
	{
		foreach ($arrSourcesMap as $strCc => $arrJsSources)
		{	
			if (count($arrJsSources))
			{
				if ($blnUserLoggedIn)
				{
					foreach ($arrJsSources as $arrSource)
					{
						$arrSources['js'][] = array
						(
							'src'      => $arrSource[$arrSource['type']],
							'cc'       => $strCc != '-' ? $strCc : '',
							'external' => $arrSource['external']
						);
					}
					continue;
				}
				
				$strFile = $this->calculateTempFile('js', $strCc . '.' . get_class($this->JsMinimizer), $arrJsSources, $GLOBALS['TL_CONFIG']['additional_sources_js_compression']);
				$strFileGz = $strFile . '.gz';
				if (!file_exists(TL_ROOT . '/' . $strFile))
				{
					$strJs = '';
					foreach ($arrJsSources as $arrSource)
					{
						switch ($arrSource['type'])
						{
						case 'js_file':
							$objFile = new File($arrSource['js_file']);
							$strContent = $objFile->getContent();
							$strContent = $this->decompressGzip($strContent);
							
							$strJs .= $strContent . "\n";
							break;
						
						case 'js_url':
							if ($arrSource['js_url_real_path'])
							{
								$strContent = file_get_contents($arrSource['js_url_real_path']);
							}
							else
							{
								$strContent = file_get_contents($this->DomainLink->absolutizeUrl($arrSource['js_url']));
							}
							$strContent = $this->decompressGzip($strContent);
													
							$strJs .= $strContent . "\n";
							break;
						}
					}
					
					// minify
					if (!$this->JsMinimizer->minimizeToFile($strFile, $strJs))
					{
						$objFile = new File($strFile);
						$objFile->write($strJs);
						$objFile->close();
					}
					
					// always create the gzip compressed version
					if (!$GLOBALS['TL_CONFIG']['additional_sources_gz_compression_disabled'])
					{
						$this->GzipCompressor->compress($strFile, $strFileGz);
					}
				}
				
				$arrSources['js'][] = array
				(
					'src'      => $strFile,
					'cc'       => $strCc != '-' ? $strCc : '',
					'external' => false
				);
			}
		}
	}
}

?>