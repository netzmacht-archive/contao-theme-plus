<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2012 InfinitySoft <http://www.infinitysoft.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Class LocalJavaScriptFile
 */
class LocalJavaScriptFile extends LocalThemePlusFile implements JavaScriptFile
{
	/**
	 * jQuery framework context
	 */
	const JQUERY = 'jquery';


	/**
	 * MooTools framework context
	 */
	const MOOTOOLS = 'mootools';


	/**
	 * The include position
	 *
	 * @var string
	 */
	protected $strPosition;


	/**
	 * The processed temporary file path.
	 *
	 * @var string
	 */
	protected $strProcessedFile;


	/**
	 * The execution framework context.
	 *
	 * @var string
	 */
	protected $strFrameworkContext = null;


	/**
	 * Create a new javascript file object.
	 *
	 * @param string $strOriginFile
	 */
	public function __construct($strOriginFile)
	{
		if ($strOriginFile[0] == '!') {
			$this->strProcessedFile = $strOriginFile = substr($strOriginFile, 1);
		}
		else
		{
			$this->strProcessedFile = null;
		}

		parent::__construct($strOriginFile);
	}


	/**
	 * Set the include position.
	 *
	 * @param string $strPosition
	 *
	 * @return void
	 */
	public function setPosition($strPosition)
	{
		$this->strPosition = $strPosition;
	}


	/**
	 * Get the include position.
	 *
	 * @return string
	 */
	public function getPosition()
	{
		return $this->strPosition;
	}


	/**
	 * Set the
	 * @param string $strFrameworkContext
	 */
	public function setFrameworkContext($strFrameworkContext)
	{
		$this->strFrameworkContext = $strFrameworkContext;
	}


	/**
	 * @return string
	 */
	public function getFrameworkContext()
	{
		return $this->strFrameworkContext;
	}


	/**
	 * @see ThemePlusFile::getDebugComment
	 * @return string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($GLOBALS['TL_CONFIG']['debugMode'] || $this->ThemePlus->getBELoginStatus()) {
			return '<!-- local file: ' . $this->getOriginFile() . ', position: ' . $this->getPosition() . ', aggregation: ' . $this->getAggregation() . ', scope: ' . $this->getAggregationScope() . ' -->' . "\n";
		}
		return '';
	}


	/**
	 * Wrap into specific framework context.
	 */
	protected function wrapFrameworkContext($strContent)
	{
		switch ($this->strFrameworkContext)
		{
			case self::JQUERY:
				$strContent = sprintf('(function($){ %s })(jQuery);', $strContent);
				break;

			case self::MOOTOOLS:
				$strContent = sprintf('(function($, $$){ %s })(document.id, document.search);', $strContent);
				break;
		}

		return $strContent;
	}


	/**
	 * @see LocalThemePlusFile::getFile
	 * @return string
	 */
	public function getFile()
	{
		if ($this->strProcessedFile == null) {
			$this->import('Compression');

			$strJsMinimizer = $this->ThemePlus->getBELoginStatus() ? false : $this->Compression->getDefaultJsMinimizer();
			if (!$strJsMinimizer) {
				$strJsMinimizer = 'none';
			}

			$objFile = new File($this->strOriginFile);
			$strTemp = $objFile->value
				. '-' . $objFile->mtime
				. '-' . $strJsMinimizer
				. '-' . ($this->strFrameworkContext ? $this->strFrameworkContext : 'global')
				. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
			$strTemp = sprintf('system/scripts/%s-%s.js', $objFile->filename, substr(md5($strTemp), 0, 8));

			if (!file_exists(TL_ROOT . '/' . $strTemp)) {
				$this->import('Compression');

				// import the Theme+ master class
				$this->import('ThemePlus');

				// import the javascript minimizer
				$this->Minimizer = $this->Compression->getJsMinimizer($strJsMinimizer);

				// import the gzip compressor
				$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
				$this->import($strGzipCompressorClass, 'Compressor');

				$strContent = $objFile->getContent();

				// detect and decompress gziped content
				$strContent = $this->ThemePlus->decompressGzip($strContent);

				// replace variables
				$strContent = $this->ThemePlus->replaceVariablesByTheme($strContent, $this->objTheme, $strTemp);

				// replace insert tags
				$strContent = $this->replaceInsertTags($strContent);

				// wrap context
				$strContent = $this->wrapFrameworkContext($strContent);

				// minify
				if (!$this->Minimizer->minimizeToFile($strTemp, $strContent)) {
					// write unminified code, if minify failed
					$objTemp = new File($strTemp);
					$objTemp->write($strContent);
					$objTemp->close();
				}

				// create the gzip compressed version
				if ($GLOBALS['TL_CONFIG']['gzipScripts']) {
					$this->Compressor->compress($strTemp, $strTemp . '.gz');
				}
			}

			$this->strProcessedFile = $strTemp;
		}

		return $this->strProcessedFile;
	}


	/**
	 * @see ThemePlusFile::getEmbeddedHtml
	 * @return string
	 */
	public function getEmbeddedHtml($blnLazy = false)
	{
		global $objPage;

		// get the file
		$strFile = $this->getFile();
		$objFile = new File($strFile);

		// get the css code
		$strContent = $objFile->getContent();

		// return html code
		if ($blnLazy) {
			return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . "\n" . $this->ThemePlus->wrapJavaScriptLazyEmbedded($strContent) . "\n" . '</script>');
		}
		else
		{
			return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . "\n" . $strContent . "\n" . '</script>');
		}
	}


	/**
	 * @see ThemePlusFile::getIncludeHtml
	 * @return string
	 */
	public function getIncludeHtml($blnLazy = false)
	{
		global $objPage;

		// get the file
		$strFile = $this->getFile();

		// return html code
		if ($blnLazy) {
			return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . "\n" . $this->ThemePlus->wrapJavaScriptLazyInclude(TL_SCRIPT_URL . $strFile) . "\n" . '</script>');
		}
		else
		{
			return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . TL_SCRIPT_URL . specialchars($strFile) . '"></script>');
		}
	}


	/**
	 * Convert into a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getOriginFile() . '|' . $this->getCc() . '|' . $this->getPosition();
	}
}
