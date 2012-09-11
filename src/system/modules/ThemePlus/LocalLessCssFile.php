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
 * Class LocalLessCssFile
 */
class LocalLessCssFile extends LocalCssFile
{

	/**
	 * Create a new css file object.
	 *
	 * @param string $strOriginFile
	 * @param string $strMedia
	 * @param string $strCc
	 * @param Database_Result $objTheme
	 * @param Database_Result $objAbsolutizePage
	 */
	public function __construct($strOriginFile)
	{
		parent::__construct($strOriginFile);
	}


	/**
	 * Check for client side compilation.
	 *
	 * @return bool
	 */
	protected function isClientSideCompile()
	{
		switch ($GLOBALS['TL_CONFIG']['theme_plus_lesscss_mode'])
		{
			case 'phpless':
				return false;

			case 'less.js':
				return true;

			case 'less.js+pre':
				return $this->ThemePlus->getBELoginStatus();
		}
	}


	/**
	 * Get the file path relative to TL_ROOT
	 *
	 * @return string
	 */
	public function getFile()
	{
		if ($this->strProcessedFile == null) {
			if ($this->isClientSideCompile()) {
				// add client side javascript
				if ($this->ThemePlus->getBELoginStatus()) {
					$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.development.js';
				}
				else
				{
					$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.js';
				}

				$objFile = new File($this->strOriginFile);
				$strKey  = $objFile->value
					. '-' . $this->strMedia
					. '-' . ($this->objAbsolutizePage != null ? 'absolute' : 'relative')
					. '-' . $objFile->mtime
					. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
				$strTemp = sprintf('system/scripts/%s-%s.less', $objFile->filename, substr(md5($strKey), 0, 8));

				if (!file_exists(TL_ROOT . '/' . $strTemp)) {
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

					// replace insert tags
					$strContent = $this->replaceInsertTags($strContent);

					// embed images
					if ($GLOBALS['TL_CONFIG']['css_embed_images'] > 0) {
						$this->import('CssInlineImages');
						$strContent = $this->CssInlineImages->embedCode($strContent, $objFile, $GLOBALS['TL_CONFIG']['css_embed_images']);
					}

					// remap url(..) entries
					$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strTemp, $this->objAbsolutizePage != null, $this->objAbsolutizePage);

					// add media definition
					if (strlen($this->strMedia)) {
						$strContent = sprintf("@media %s\n{\n%s\n}\n", $this->strMedia, $strContent);
					}

					// add @charset utf-8 rule
					$strContent = '@charset "UTF-8";' . "\n" . $strContent;

					// write code
					$objTemp = new File($strTemp);
					$objTemp->write($strContent);
					$objTemp->close();
				}

				$this->strProcessedFile = $strTemp;
			}
			else
			{
				$this->import('Compression');

				$strCssMinimizer = $this->ThemePlus->getBELoginStatus() ? false : $this->Compression->getDefaultCssMinimizer();
				if (!$strCssMinimizer) {
					$strCssMinimizer = 'none';
				}

				$objFile = new File($this->strOriginFile);
				$strKey  = $objFile->value
					. '-' . $this->strMedia
					. '-' . ($this->objAbsolutizePage != null ? 'absolute' : 'relative')
					. '-' . $objFile->mtime
					. '-' . $strCssMinimizer
					. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
				$strTemp = sprintf('system/scripts/%s-%s.css', $objFile->filename, substr(md5($strKey), 0, 8));

				if (!file_exists(TL_ROOT . '/' . $strTemp)) {
					$this->import('Compression');

					// import the css minimizer
					$this->Minimizer = $this->Compression->getCssMinimizer($strCssMinimizer);

					// import the gzip compressor
					$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
					$this->import($strGzipCompressorClass, 'Compressor');

					// import the url remapper
					$this->import('CssUrlRemapper');

					// import the less compiler
					switch ($GLOBALS['TL_CONFIG']['theme_plus_lesscss_mode'])
					{
						case 'less.js+pre':
							$this->import('LessCss', 'Compiler');
							break;

						case 'phpless':
							$this->import('PHPLessCss', 'Compiler');
							$this->Compiler->setImportDir(dirname($objFile->value));
							break;

						default:
							throw new Exception('Unsupported less mode!');
					}

					// get the css code
					$strContent = $objFile->getContent();

					// detect and decompress gziped content
					$strContent = $this->ThemePlus->decompressGzip($strContent);

					// handle @charset
					$strContent = $this->ThemePlus->handleCharset($strContent);

					// replace variables
					$strContent = $this->ThemePlus->replaceVariablesByTheme($strContent, $this->objTheme, $strTemp);

					// replace insert tags
					$strContent = $this->replaceInsertTags($strContent);

					// embed images
					if ($GLOBALS['TL_CONFIG']['css_embed_images'] > 0) {
						$this->import('CssInlineImages');
						$strContent = $this->CssInlineImages->embedCode($strContent, $objFile, $GLOBALS['TL_CONFIG']['css_embed_images']);
					}

					// remap url(..) entries
					$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strTemp, $this->objAbsolutizePage != null, $this->objAbsolutizePage);

					// write temporary source file
					$objSource = new File(sprintf('system/scripts/%s-%s.less', $objFile->filename, substr(md5($strKey), 0, 8)));
					$objSource->write($strContent);
					$objSource->close();

					// compile with less
					$strContent = $this->Compiler->minimizeFromFile($objSource->value);

					// if compile fails, return origin file
					if ($strContent === false) {
						return $this->strOriginFile;
					}

					// add media definition
					if (strlen($this->strMedia)) {
						$strContent = sprintf("@media %s\n{\n%s\n}\n", $this->strMedia, $strContent);
					}

					// add @charset utf-8 rule
					$strContent = '@charset "UTF-8";' . "\n" . $strContent;

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
		}

		return $this->strProcessedFile;
	}


	/**
	 * @see ThemePlusFile::getEmbeddedHtml
	 * @return string
	 */
	public function getEmbeddedHtml($blnLazy = false)
	{
		if ($this->isClientSideCompile()) {
			return $this->getDebugComment() . $this->getIncludeHtml($blnLazy);
		}
		else
		{
			return parent::getEmbeddedHtml($blnLazy);
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
		return $this->getDebugComment() . $this->wrapCc('<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' rel="' . (preg_match('#\.less$#i', $strFile) ? 'stylesheet/less' : 'stylesheet') . '" href="' . TL_SCRIPT_URL . specialchars($strFile) . '" />');
	}


	/**
	 * @see ThemePlusFile::isAggregateable
	 * @return bool
	 */
	public function isAggregateable()
	{
		return $this->isClientSideCompile() || strlen($this->strCc) ? false : true;
	}
}
