<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalCssFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LocalCssFile extends LocalThemePlusFile {
	
	/**
	 * The media selectors.
	 */
	protected $arrMedia;
	
	
	/**
	 * The absolutize page.
	 */
	protected $objAbsolutizePage;
	
	
	/**
	 * Create a new css file object.
	 */
	public function __construct($strOriginFile, $arrMedia, $objAbsolutizePage = false)
	{
		parent::__construct($strOriginFile);
		$this->arrMedia = $arrMedia;
		$this->objAbsolutizePage = $objAbsolutizePage;
	}

	
	public function getFile()
	{
		if ($this->strProcessedFile == null)
		{
			if (ThemePlus::getBELoginStatus())
			{
				$this->strProcessedFile = $this->strOriginFile;
			}
			else
			{
				$strCssMinimizer = $this->Compression->getDefaultCssMinimizer();
				
				$objFile = new File($this->strOriginFile);
				$strKey = sprintf("%s-%s-%s-%s-%s",
						$objFile->basename,
						implode(',', $this->arrMedia),
						$this->objAbsolutizePage != null ? 'absolute' : 'relative', 
						$objFile->mtime,
						$strCssMinimizer);
				$strTemp = sprintf('system/modules/%s-%s.css', $objFile->filename, substr(md5($strKey), 0, 8));
				
				if (!file_exists(TL_ROOT . '/' . $strTemp))
				{
					$this->import('Compression');
					
					// import the css minimizer
					$strCssMinimizerClass = $this->Compression->getDefaultCssMinimizerClass();
					$this->import($strCssMinimizerClass, 'Minimizer');
					
					// import the gzip compressor
					$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
					$this->import($strGzipCompressorClass, 'Compressor');
					
					// import the url remapper
					$this->import('CssUrlRemapper');
					
					// get the css code
					$strContent = $objFile->getContent();
					
					// detect and decompress gziped content
					$strContent = ThemePlus::decompressGzip($strContent);
					
					// handle @charset
					$strContent = $this->handleCharset($strContent);

					// add media definition
					if (count($this->arrMedia))
					{
						$strContent = sprintf("@media %s\n{\n%s\n}\n", implode(',', $arrMedia), $strContent);
					}
					
					// add @charset utf-8 rule
					$strContent = '@charset "UTF-8";' . "\n" . $strContent;
										
					// remap url(..) entries
					$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strTemp, $this->objAbsolutizePage ? true : false, $this->objAbsolutizePage);
					
					// minify
					if (!$this->Minimizer->minimizeToFile($strTemp, $strContent))
					{
						// write unminified code, if minify failed
						$objTemp = new File($strTemp);
						$objTemp->write($strContent);
						$objTemp->close();
					}
					
					// create the gzip compressed version
					if (!$GLOBALS['TL_CONFIG']['theme_plus_gz_compression_disabled'])
					{
						$this->Compressor->compress($strTemp, $strTemp . '.gz');
					}
				}
				
				$this->strProcessedFile = $strTemp;
			}
		}
		
		return $this->strProcessedFile;
	}
	
	
	public function getEmbededHtml()
	{
		// get the file
		$strFile = $this->getFile();
		$objFile = new File($strFile);
		
		// get the css code
		$strContent = $objFile->getContent();
		
		// handle @charset
		$strContent = $this->handleCharset($strContent);
		
		// return html code
		return '<style type="text/css">' . $strContent . '</style>';
	}
	
	
	public function getIncludeHtml()
	{
		// get the file
		$strFile = $this->getFile();
		
		// return html code
		return '<link type="text/css" rel="stylesheet" href="' . specialchars($strFile) . '" />';
	}
}

?>