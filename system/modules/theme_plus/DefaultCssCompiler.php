<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


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
	
	public function compile($arrSourcesMap, &$arrSources, $blnUserLoggedIn, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		foreach ($arrSourcesMap as $strCc => $arrCssSources)
		{
			if (count($arrCssSources))
			{
				if ($blnUserLoggedIn)
				{
					foreach ($arrCssSources as $arrSource)
					{
						$arrSources['css'][] = array
						(
							'src'      => $arrSource[$arrSource['type']],
							'cc'       => $strCc != '-' ? $strCc : '',
							'external' => $arrSource['external'],
							'media'    => implode(',', deserialize($arrSource['media'], true))
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
							$strContent = $this->handleCharset($strContent);
							
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
							$strContent = $this->handleCharset($strContent);
							
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