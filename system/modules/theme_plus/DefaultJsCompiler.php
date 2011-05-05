<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


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