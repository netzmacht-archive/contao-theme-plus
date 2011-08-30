<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalCssFile
 */
class LocalCssFile extends LocalThemePlusFile {

	/**
	 * The media selectors.
	 */
	protected $strMedia;


	/**
	 * The absolutize page.
	 */
	protected $objAbsolutizePage;


	/**
	 * The processed temporary file path.
	 */
	protected $strProcessedFile;


	/**
	 * Create a new css file object.
	 */
	public function __construct($strOriginFile, $strMedia = '', $strCc = '', $objTheme = false, $objAbsolutizePage = false)
	{
		parent::__construct($strOriginFile, $strCc, $objTheme);
		$this->strMedia = $strMedia;
		$this->objAbsolutizePage = $objAbsolutizePage;
		$this->strProcessedFile = null;

		// import the Theme+ master class
		$this->import('ThemePlus');
	}


	public function getMedia()
	{
		return $this->strMedia;
	}


	public function getFile()
	{
		if ($this->strProcessedFile == null)
		{
			$this->import('Compression');

			$strCssMinimizer = $this->ThemePlus->getBELoginStatus() ? false : $this->Compression->getDefaultCssMinimizer();
			if (!$strCssMinimizer)
			{
				$strCssMinimizer = 'none';
			}

			$objFile = new File($this->strOriginFile);
			$strKey = $objFile->basename
					. '-' . $this->strMedia
					. '-' . ($this->objAbsolutizePage != null ? 'absolute' : 'relative')
					. '-' . $objFile->mtime
					. '-' . $strCssMinimizer
					. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
			$strTemp = sprintf('system/scripts/%s-%s.css', $objFile->filename, substr(md5($strKey), 0, 8));

			if (!file_exists(TL_ROOT . '/' . $strTemp))
			{
				$this->import('Compression');

				// import the css minimizer
				$this->Minimizer = $this->Compression->getCssMinimizer($strCssMinimizer);

				// import the gzip compressor
				$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
				$this->import($strGzipCompressorClass, 'Compressor');

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
				$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strTemp, $this->objAbsolutizePage ? true : false, $this->objAbsolutizePage);

				// replace insert tags
				$strContent = $this->replaceInsertTags($strContent);

				// minify
				if (!$this->Minimizer->minimizeToFile($strTemp, $strContent))
				{
					// write unminified code, if minify failed
					$objTemp = new File($strTemp);
					$objTemp->write($strContent);
					$objTemp->close();
				}

				// create the gzip compressed version
				if ($GLOBALS['TL_CONFIG']['gzipScripts'])
				{
					$this->Compressor->compress($strTemp, $strTemp . '.gz');
				}
			}

			$this->strProcessedFile = $strTemp;
		}

		return $this->strProcessedFile;
	}


	public function getGlobalVariableCode()
	{
		return $this->getFile() . (strlen($this->strMedia) ? '|' . $this->strMedia : '') . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}


	public function getEmbededHtml()
	{
		global $objPage;

		// get the file
		$strFile = $this->getFile();
		$objFile = new File($strFile);

		// get the css code
		$strContent = $objFile->getContent();

		// handle @charset
		$strContent = $this->ThemePlus->handleCharset($strContent);

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<style' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . '>' . $strContent . '</style>');
	}


	public function getIncludeHtml()
	{
		global $objPage;

		// get the file
		$strFile = $this->getFile();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' rel="stylesheet" href="' . TL_SCRIPT_URL . specialchars($strFile) . '" />');
	}


	/**
	 * Convert into a string.
	 */
	public function __toString()
	{
		return $this->getOriginFile() . '|' . $this->getMedia() . '|' . $this->getCc();
	}
}

?>