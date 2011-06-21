<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class CssFileAggregator
 */
class CssFileAggregator extends LocalThemePlusFile {
	
	/**
	 * The files to aggregate.
	 */
	protected $arrFiles;
	
	
	/**
	 * The absolutize page.
	 */
	protected $objAbsolutizePage;
	
	
	/**
	 * The aggregated file.
	 */
	protected $strAggregatedFile;
	
	
	/**
	 * Create a new css file object.
	 */
	public function __construct()
	{
		$args = func_get_args();
		if (!($args[count($args)-1] instanceof ThemePlusFile))
		{
			$this->objAbsolutizePage = array_pop($args);
		}
		else
		{
			$this->objAbsolutizePage = false;
		}
		$this->arrFiles = $args;
		$this->strAggregatedFile = null;
		
		// import the Theme+ master class
		$this->import('ThemePlus');
	}
	
	
	/**
	 * Add a file.
	 */
	public function add(LocalCssFile $objFile)
	{
		$this->arrFiles[] = $objFile;
	}

	
	public function getFile()
	{
		if ($this->strAggregatedFile == null)
		{
			$arrFiles = array();
			$strKey = count($this->arrFiles);
			foreach ($this->arrFiles as $objThemePlusFile)
			{
				if ($objThemePlusFile instanceof LocalCssFile)
				{
					if ($objThemePlusFile->isAggregateable())
					{
						$strFile = $objThemePlusFile->getFile();
						$objFile = new File($strFile);
						$arrFiles[] = $strFile;
						$strKey .= sprintf(':%s-%d', basename($strFile, '.css'), $objFile->mtime);
						continue; 
					}
				}
				throw new Exception('Could not aggreagate the file: ' . $objFile);
			}
			
			$strTemp = 'system/html/stylesheet-' . substr(md5($strKey), 0, 8) . '.css';
			
			if (!file_exists(TL_ROOT . '/' . $strTemp))
			{
				$this->import('Compression');
				
				// import the gzip compressor
				$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
				$this->import($strGzipCompressorClass, 'Compressor');
				
				// import the url remapper
				$this->import('CssUrlRemapper');
				
				// build the content
				$strContent = '@charset "UTF-8";' . "\n";
				
				foreach ($arrFiles as $strFile)
				{
					$objFile = new File($strFile);
					
					// get the css code
					$strSubContent = $objFile->getContent();
					
					// detect and decompress gziped content
					$strSubContent = $this->ThemePlus->decompressGzip($strSubContent);
					
					// handle @charset
					$strSubContent = $this->ThemePlus->handleCharset($strSubContent);
									
					// remap url(..) entries
					$strSubContent = $this->CssUrlRemapper->remapCode($strSubContent, $strFile, $strTemp, $this->objAbsolutizePage ? true : false, $this->objAbsolutizePage);
					
					// append to content
					$strContent .= "\n" . $strSubContent;
				}
				
				// write the file
				$objTemp = new File($strTemp);
				$objTemp->write($strContent);
				$objTemp->close();
				
				// create the gzip compressed version
				if (!$GLOBALS['TL_CONFIG']['theme_plus_gz_compression_disabled'])
				{
					$this->Compressor->compress($strTemp, $strTemp . '.gz');
				}
			}
			
			$this->strAggregatedFile = $strTemp;
		}
		return $this->strAggregatedFile;
	}
	
	
	public function getEmbededHtml()
	{
		// get the file
		$strFile = $this->getFile();
		$objFile = new File($strFile);
		
		// get the css code
		$strContent = $objFile->getContent();
		
		// handle @charset
		$strContent = $this->ThemePlus->handleCharset($strContent);
		
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