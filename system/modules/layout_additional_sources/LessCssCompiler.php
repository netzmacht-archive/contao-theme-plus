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
		return $objFile->filename . '.' . substr(md5($strKey), 0, 8);
	}
	
	
	/**
	 * Compile the css files and add them to the layout.
	 * 
	 * @param array $arrSourcesMap
	 * @param array $arrSources
	 * @param bool $blnUserLoggedIn
	 * @param bool $blnAbsolutizeUrls
	 * @param Database_Result $objAbsolutizePage
	 */
	public function compile($arrSourcesMap, &$arrSources, $blnUserLoggedIn, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		if ($blnUserLoggedIn || $GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'less.js')
		{
			$this->compileClientSide($arrSourcesMap, $arrSources, $blnUserLoggedIn, $blnAbsolutizeUrls, $objAbsolutizePage);
		}
		else
		{
			$this->compileServerSide($arrSourcesMap, $arrSources, $blnUserLoggedIn, $blnAbsolutizeUrls, $objAbsolutizePage);
		}
	}
	
	
	/**
	 * Prepare the css files for client side compilation via javascript.
	 * 
	 * @param array $arrSourcesMap
	 * @param array $arrSources
	 * @param bool $blnUserLoggedIn
	 * @param bool $blnAbsolutizeUrls
	 * @param Database_Result $objAbsolutizePage
	 * @throws Exception
	 */
	protected function compileClientSide($arrSourcesMap, &$arrSources, $blnUserLoggedIn, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$blnLess = false;
		
		foreach ($arrSourcesMap as $strCc => $arrCssSources)
		{
			if (count($arrCssSources))
			{
				foreach ($arrCssSources as $arrSource)
				{
					$arrTemp = false;
					
					switch ($arrSource['type'])
					{
					case 'css_file':
						$arrTemp = array
						(
							'src'      => $arrSource['css_file'],
							'cc'       => $strCc != '-' ? $strCc : '',
							'external' => false,
						);
						break;
					
					case 'css_url':
						$arrTemp = array
						(
							'src'      => $arrSource['css_url'],
							'cc'       => $strCc != '-' ? $strCc : '',
							'external' => true
						);
						break;
					}
					
					if ($arrTemp)
					{
						if (preg_match('#\.less$#i', $arrTemp['src']))
						{
							$blnLess = true;
							$arrTemp['rel'] = 'stylesheet/less';
						}
						$arrMedia = deserialize($arrSource['media'], true);
						if (count($arrMedia))
						{
							$arrTemp['media'] = implode(',', $arrMedia);
						}
						$arrSources['css'][] = $arrTemp;
					}
				}
			}
		}
		
		if ($blnLess)
		{
			$arrSources['js'][] = array
			(
				'src'      => 'system/modules/lesscss/html/less-1.0.41.min.js',
				'cc'       => '',
				'external' => false
			);
		}
	}
	
	
	/**
	 * Compile the less files on server via lessc and add a precompiled css file to layout.
	 * 
	 * @param array $arrSourcesMap
	 * @param array $arrSources
	 * @param bool $blnUserLoggedIn
	 * @param bool $blnAbsolutizeUrls
	 * @param Database_Result $objAbsolutizePage
	 * @throws Exception
	 */
	protected function compileServerSide($arrSourcesMap, &$arrSources, $blnUserLoggedIn, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$this->import('Compression');
		
		$this->import('LessCss');
		
		if (preg_match('#^less\.js\+(\w+)$#', $GLOBALS['TL_CONFIG']['additional_sources_css_compression'], $m) && $m[1] != 'pre')
		{
			$strCssMinimizerClass = $this->Compression->getCssMinimizerClass($m[1]);
			$this->import($strCssMinimizerClass, 'CssMinimizer');
			
			$this->LessCss->configure(array('compress'=>false));
		}
		else
		{
			$this->CssMinimizer = false;
		}
		
		$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
		$this->import($strGzipCompressorClass, 'GzipCompressor');
		
		$this->import('CssUrlRemapper');
		
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
							$strCompilerFile = 'system/html/' . $strCompilerName . '.css';
							
							if (!file_exists(TL_ROOT . '/' . $strCompilerFile))
							{
								$objCompilerFile = new File($strCompilerFile);
								
								$strCompilerSource = 'system/html/source_' . $strCompilerName . '.less';
								$objCompilerSource = new File($strCompilerSource);
								
								$strContent = $objFile->getContent();
								$strContent = $this->decompressGzip($strContent);
								
								// handle @charset
								$strContent = $this->handleCharset($strContent);
								
								// remap url(..) entries
								$strContent = $this->CssUrlRemapper->remapCode($strContent, $arrSource['css_file'], $objCompilerSource->value, $blnAbsolutizeUrls, $objAbsolutizePage);
								
								// write the temporary source file
								$objCompilerSource->write('@charset "UTF-8";' . "\n" . trim($strContent));
								
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
									$strContent = $this->handleCharset($strContent);
									$strContent = sprintf('@media %s{%s}', implode(',', $arrMedia), $strContent);
									$objCompilerFile->write($strContent);
								}
								
								// delete temporary source file
								#$objCompilerSource->delete();
							}
							else
							{
								$objCompilerFile = new File($strCompilerFile);
							}
							
							$arrData[] = $objCompilerFile;
							break;
						
						case 'css_url':
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
							$strContent = $this->handleCharset($strContent);
							
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
								$strCss .= $this->handleCharset($varData) . "\n";
							}
							else if (is_a($varData, 'File'))
							{
								$strCss .= $this->handleCharset($varData->getContent()) . "\n";
							}
							else
							{
								throw new Exception('Illegal data found: ' . print_r($varData, true));
							}
						}
						
						// minify
						if (!$this->CssMinimizer || $this->CssMinimizer && !$this->CssMinimizer->minimizeToFile($strFile, $strCss))
						{
							$objFile = new File($strFile);
							$objFile->write($strCss);
							$objFile->close();
						}
						
						// create the gzip compressed version
						if (!$GLOBALS['TL_CONFIG']['additional_sources_gz_compression_disabled'])
						{
							$this->GzipCompressor->compress($strFile, $strFileGz);
						}
					}
					else
					{
							$objFile = new File($strFile);
							$objFile->write('// no css' . "\n");
							$objFile->close();
					}
				}
						
				$arrSources['css'][] = array
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