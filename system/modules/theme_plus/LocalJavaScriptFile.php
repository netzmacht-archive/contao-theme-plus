<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalJavaScriptFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LocalJavaScriptFile extends LocalThemePlusFile {
	
	/**
	 * Create a new javascript file object.
	 */
	public function __construct($strOriginFile)
	{
		parent::__construct($strOriginFile);
	}


	/**
	 * Get the file path relative to TL_ROOT
	 */
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
				$strJsMinimizer = $this->Compression->getDefaultJsMinimizer();
				
				$objFile = new File($this->strOriginFile);
				$strTemp = sprintf("%s-%s-%s",
						$objFile->basename,
						$objFile->mtime,
						$strJsMinimizer);
				$strTemp = sprintf('system/modules/%s-%s.js', $objFile->filename, substr(md5($strTemp), 0, 8));
				
				if (!file_exists(TL_ROOT . '/' . $strTemp))
				{
					$this->import('Compression');
					
					// import the javascript minimizer
					$strJsMinimizerClass = $this->Compression->getDefaultJsMinimizerClass();
					$this->import($strJsMinimizerClass, 'Minimizer');
					
					// import the gzip compressor
					$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
					$this->import($strGzipCompressorClass, 'Compressor');
					
					$strContent = $objFile->getContent();
					
					// detect and decompress gziped content
					$strContent = ThemePlus::decompressGzip($strContent);
					
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
	
	
	/**
	 * Get embeded html code
	 */
	public function getEmbededHtml()
	{
		// get the file
		$strFile = $this->getFile();
		$objFile = new File($strFile);
		
		// get the css code
		$strContent = $objFile->getContent();
		
		// return html code
		return '<script type="text/javascript">' . $strContent . '</script>';
	}
	
	
	/**
	 * Get included html code
	 */
	public function getIncludeHtml()
	{
		// get the file
		$strFile = $this->getFile();
		
		// return html code
		return '<script type="text/javascript" src="' . specialchars($strFile) . '"></script>';
	}
	
}

?>