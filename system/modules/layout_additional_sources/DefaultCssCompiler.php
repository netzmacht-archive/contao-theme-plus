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
 * Class DefaultCssCompiler
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class DefaultCssCompiler extends CompilerBase {
	public function __construct()
	{
		$this->import('Compression');
		
		$strCssMinimizerClass = $this->Compression->getCssMinimizerClass($GLOBALS['TL_CONFIG']['additional_sources_css_compression']);
		$this->import($strCssMinimizerClass, 'CssMinimizer');
		
		$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
		$this->import($strGzipCompressorClass, 'GzipCompressor');
		
		$this->import('CssUrlRemapper');
	}
	
	public function compile($arrSourcesMap, &$arrCss, $blnUserLoggedIn)
	{
		foreach ($arrSourcesMap as $strCc => $arrCssSources)
		{
			if (count($arrCssSources))
			{
				if ($blnUserLoggedIn)
				{
					foreach ($arrCssSources as $arrSource)
					{
						$arrCss[] = array
						(
							'src'      => $arrSource[$arrSource['type']],
							'cc'       => $strCc != '-' ? $strCc : '',
							'external' => $arrSource['external']
						);
					}
					continue;
				}
				
				$strFile = $this->calculateTempFile('css', $strCc . '.' . get_class($this->CssMinimizer), $arrCssSources, $GLOBALS['TL_CONFIG']['additional_sources_css_compression']);
				$strFileGz = $strFile . '.gz';
				if (!file_exists(TL_ROOT . '/' . $strFile))
				{
					// add charset definition
					$strCss = '@charset "UTF-8";' . "\n";
					
					// add the css code
					foreach ($arrCssSources as $arrSource)
					{
						switch ($arrSource['type'])
						{
						case 'css_file':
							$objFile = new File($arrSource['css_file']);
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
							$strContent = $this->CssUrlRemapper->remapCode($strContent, $arrSource['css_file'], $strFile, $blnAbsolutizeUrls, $objAbsolutizePage);
						
							// add media definition
							$arrMedia = deserialize($arrSource['media'], true);
							if (count($arrMedia))
							{
								$strContent = sprintf('@media %s{%s}', implode(',', $arrMedia), $strContent);
							}
							
							$strCss .= trim($strContent) . "\n";
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
							
							$strCss .= trim($strContent) . "\n";
							break;
						}
					}
			
					// minify
					if (!$this->CssMinimizer->minimizeToFile($strFile, $strCss))
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

?>