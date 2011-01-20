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
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Class LessCssCompiler
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LessCssCompiler extends CompilerBase {
	public function __construct()
	{
		$this->import('Compression');
		
		if ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'less+yui')
		{
			$strCssMinimizerClass = $this->Compression->getCssMinimizerClass('yui');
			$this->import($strCssMinimizerClass, 'Yui');
		}
		else
		{
			$this->Yui = false;
		}
		
		$this->import('LessCss');
		$this->LessCss->configure(array('remove-charset'=>false));
		
		$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
		$this->import($strGzipCompressorClass, 'GzipCompressor');
		
		$this->import('CssUrlRemapper');
	}
	
	
	/**
	 * Calculate a temporary combined file name. 
	 * 
	 * @param string $strGroup
	 * @param string $strCc
	 * @param array $arrAdditionalSources
	 * @return string
	 */
	protected function calculateTempCompilerName($strCc, File $objFile)
	{
		$strKey = $objFile->value . '.' . $strCc . '.' . $objFile->mtime;
		return $objFile->filename . '.' . substr(md5($strKey), 0, 8) . '.' . $objFile->extension;
	}
	
	
	public function compile($arrSourcesMap, &$arrCss, $blnUserLoggedIn)
	{
		foreach ($arrSourcesMap as $strCc => $arrCssSources)
		{
			if (count($arrCssSources))
			{
				$strFile = $this->calculateTempFile('css', $strCc . 'LessCss', $arrCssSources, $GLOBALS['TL_CONFIG']['additional_sources_css_compression']);
				$strFileGz = $strFile . '.gz';
				if (!file_exists(TL_ROOT . '/' . $strFile))
				{
					$arrData = array();
					foreach ($arrCssSources as $arrSource)
					{
						switch ($arrSource['type'])
						{
						case 'css_file':
							$objFile = new File($arrSource['css_file']);
							
							$strCompilerName = $this->calculateTempCompilerName($strCc, $objFile);
							$strCompilerFile = 'system/html/' . $strCompilerName;
							
							if (!file_exists(TL_ROOT . '/' . $strCompilerFile))
							{
								$objCompilerFile = new File($strCompilerFile);
								
								$strCompilerSource = 'system/html/source_' . $strCompilerName;
								$objCompilerSource = new File($strCompilerSource);
								
								$strContent = $objFile->getContent();
								$strContent = $this->decompressGzip($strContent);
								
								// handle @charset
								if (preg_match('#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui', $strContent, $arrMatch))
								{
									// convert character encoding to utf-8
									if (strtoupper($arrMatch[1]) != 'UTF-8')
									{
										$strContent = iconv(strtoupper($arrMatch[1]), 'UTF-8', $strContent);
									}
									// remove @charset tag
									$strContent = str_replace($arrMatch[0], '', $strContent);
								}
								
								// remap url(..) entries
								// this is done by minimizeToFile
								$strContent = $this->CssUrlRemapper->remapCode($strContent, $arrSource['css_file'], $objCompilerSource->value, $blnAbsolutizeUrls, $objAbsolutizePage);
								
								// write the temporary source file
								$objCompilerSource->write(trim($strContent));
								
								// compile with less
								if (!$this->LessCss->minimize($strCompilerSource, $strCompilerFile))
								{
									$objCompilerFile->write($strContent);
								}
							
								// add media definition
								$arrMedia = deserialize($arrSource['media'], true);
								if (count($arrMedia))
								{
									$strContent = $objCompilerFile->getContent();
									$strContent = sprintf('@media %s{%s}', implode(',', $arrMedia), $strContent);
									$objCompilerFile->write($strContent);
								}
								
								// delete temporary source file
								$objCompilerSource->delete();
							}
							else
							{
								$objCompilerFile = new File($strCompilerFile);
							}
							
							if ($blnUserLoggedIn)
							{
								$arrCss[] = array
								(
									'src'      => $objCompilerFile->value,
									'cc'       => $strCc != '-' ? $strCc : '',
									'external' => false
								);
								continue;
							}
							
							$arrData[] = $objCompilerFile;
							break;
						
						case 'css_url':
							if ($blnUserLoggedIn)
							{
								$arrCss[] = array
								(
									'src'      => $arrSource['css_url'],
									'cc'       => $strCc != '-' ? $strCc : '',
									'external' => true
								);
								continue;
							}
							
							if ($arrSource['css_url_real_path'])
							{
								$strContent = file_get_contents($arrSource['css_url_real_path']);
							}
							else
							{
								$strContent = file_get_contents($this->DomainLink->absolutizeUrl($arrSource['css_url']));
							}
							$strContent = $this->decompressGzip($strContent);
							
							// handle @charset
							if (preg_match('#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui', $strContent, $arrMatch))
							{
								// convert character encoding to utf-8
								if (strtoupper($arrMatch[1]) != 'UTF-8')
								{
									$strContent = iconv(strtoupper($arrMatch[1]), 'UTF-8', $strContent);
								}
								// remove @charset tag
								$strContent = str_replace($arrMatch[0], '', $strContent);
							}
							
							$arrData[] = trim($strContent);
							break;
						}
					}
					
					// combine the css code
					if (count($arrData))
					{
						// add charset definition
						$strCss = '@charset "UTF-8";' . "\n";
						
						// add the css code
						foreach ($arrData as $varData)
						{
							if (is_string($varData))
							{
								$strCss .= $varData . "\n";
							}
							else if (is_a($varData, 'File'))
							{
								$strCss .= $varData->getContent() . "\n";
							}
							else
							{
								throw new Exception('Illegal data found: ' . print_r($varData, true));
							}
						}
						
						// minify
						if (!$this->Yui || $this->Yui && !$this->Yui->minimizeToFile($strFile, $strCss))
						{
							$objFile = new File($strFile);
							$objFile->write($strCss);
							$objFile->close();
						}
						
						// always create the gzip compressed version
						if (!$GLOBALS['TL_CONFIG']['additional_sources_gz_compression_disabled'])
						{
							$this->GzipCompressor->compress($strFile, $strFileGz);
						}
					}
				}
			
				if (!$blnUserLoggedIn)
				{
					$arrCss[] = array
					(
						'src'      => $strFile,
						'cc'       => $strCc != '-' ? $strCc : '',
						'external' => false
					);
				}
			}
		}
	}
}

?>