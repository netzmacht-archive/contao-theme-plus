<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalLessCssFile
 */
class LocalLessCssFile extends LocalCssFile {
	
	/**
	 * Create a new css file object.
	 */
	public function __construct($strOriginFile, $strMedia, $objTheme = false, $objAbsolutizePage = false)
	{
		parent::__construct($strOriginFile, $strMedia, $objTheme, $objAbsolutizePage);
		
		// import the Theme+ master class
		$this->import('ThemePlus');
	}

	
	/**
	 * Check for client side compilation.
	 */
	protected function isClientSideCompile()
	{
		return ($this->ThemePlus->getBELoginStatus() || $GLOBALS['TL_CONFIG']['theme_plus_lesscss_mode'] == 'less.js') ? true : false;
	}
	
	
	/**
	 * Get the file path relative to TL_ROOT
	 */
	public function getFile()
	{
		if ($this->strProcessedFile == null)
		{
			if ($this->isClientSideCompile())
			{
				// add client side javascript
				if ($this->ThemePlus->getBELoginStatus())
				{
					$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.development.js';
				}
				else
				{
					$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.js';
				}
				
				$objFile = new File($this->strOriginFile);
				$strKey = $objFile->basename
						. '-' . $this->strMedia
						. '-' . $this->objAbsolutizePage != null ? 'absolute' : 'relative' 
						. '-' . $objFile->mtime
						. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
				$strTemp = sprintf('system/html/%s-%s.less', $objFile->filename, substr(md5($strKey), 0, 8));
				
				if (!file_exists(TL_ROOT . '/' . $strTemp))
				{
					// import the url remapper
					$this->import('CssUrlRemapper');
					
					// get the css code
					$strContent = $objFile->getContent();
					
					// detect and decompress gziped content
					$strContent = $this->ThemePlus->decompressGzip($strContent);
					
					// handle @charset
					$strContent = $this->ThemePlus->handleCharset($strContent);

					// replace variables
					$strContent = $this->ThemePlus->replaceVariablesByTheme($strContent, $this->objTheme, $strTemp);

					// add media definition
					if (strlen($this->strMedia))
					{
						$strContent = sprintf("@media %s\n{\n%s\n}\n", $this->strMedia, $strContent);
					}
					
					// add @charset utf-8 rule
					$strContent = '@charset "UTF-8";' . "\n" . $strContent;
					
					// remap url(..) entries
					$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strSource, $this->objAbsolutizePage != null, $this->objAbsolutizePage);
					
					// write unminified code, if minify failed
					$objTemp = new File($strTemp);
					$objTemp->write($strContent);
					$objTemp->close();
				}
				
				$this->strProcessedFile = $strTemp;
			}
			else
			{
				$strCssMinimizer = $this->Compression->getDefaultCssMinimizer();
				
				$objFile = new File($this->strOriginFile);
				$strKey = $objFile->basename
						. '-' . $this->strMedia
						. '-' . $this->objAbsolutizePage != null ? 'absolute' : 'relative' 
						. '-' . $objFile->mtime
						. '-' . $strCssMinimizer
						. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
				$strTemp = sprintf('system/html/%s-%s.less.css', $objFile->filename, substr(md5($strKey), 0, 8));
				
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
					
					// compile the less code
					$strCompilation = sprintf('system/html/%s-%s-compilation.less.css', $objFile->filename, substr(md5($strKey), 0, 8));
					$objCompilation = new File($strCompilation);
					
					if (!file_exists(TL_ROOT . '/' . $strCompilation))
					{
						// import the less compiler
						$this->import('LessCss', 'Compiler');
						
						// create a temporary source file
						$strSource = sprintf('system/html/%s-%s-precompilation.less', $objFile->filename, substr(md5($strKey), 0, 8));
						$objSource = new File($strSource);
						
						// get the css code
						$strContent = $objFile->getContent();
						
						// detect and decompress gziped content
						$strContent = $this->ThemePlus->decompressGzip($strContent);
						
						// handle @charset
						$strContent = $this->ThemePlus->handleCharset($strContent);

						// replace variables
						$strContent = $this->ThemePlus->replaceVariablesByTheme($strContent, $this->objTheme, $strTemp);
						
						// add media definition
						if (strlen($this->strMedia))
						{
							$strContent = sprintf("@media %s\n{\n%s\n}\n", $this->strMedia, $strContent);
						}
						
						// add @charset utf-8 rule
						$strContent = '@charset "UTF-8";' . "\n" . $strContent;
						
						// remap url(..) entries
						$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strSource, $this->objAbsolutizePage != null, $this->objAbsolutizePage);
						
						// compile with less
						if (!$this->Compiler->minimize($strSource, $strCompilation))
						{
							$objCompilation->write($strContent);
						}
					}
					
					// get the css code
					$strContent = $objCompilation->getContent();
					
					// handle @charset
					$strContent = $this->ThemePlus->handleCharset($strContent);

					// add @charset utf-8 rule
					$strContent = '@charset "UTF-8";' . "\n" . $strContent;
					
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
		if ($this->isClientSideCompile())
		{
			return $this->getIncludeHtml();
		}
		else
		{
			return parent::getEmbededHtml();
		}
	}
	
	
	public function isAggregateable()
	{
		return $this->isClientSideCompile() ? false : true;
	}
	
	
	/**
	 * Get included html code
	 */
	public function getIncludeHtml()
	{
		// get the file
		$strFile = $this->getFile();
		
		// return html code
		return '<link type="text/css" rel="' . (preg_match('#\.less$#i', $strFile) ? 'stylesheet/less' : 'stylesheet') . '" href="' . specialchars($strFile) . '" />';
	}
	
}

?>