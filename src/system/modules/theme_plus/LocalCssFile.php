<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Theme+
 * Copyright (C) 2010,2011 InfinitySoft <http://www.infinitysoft.de>
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 * @license    LGPL
 */


/**
 * Class LocalCssFile
 */
class LocalCssFile extends LocalThemePlusFile implements CssFile
{

	/**
	 * The media selectors.
	 *
	 * @var string
	 */
	protected $strMedia;


	/**
	 * The absolutize page.
	 *
	 * @var bool
	 */
	protected $objAbsolutizePage;


	/**
	 * The processed temporary file path.
	 *
	 * @var string
	 */
	protected $strProcessedFile;


	/**
	 * Create a new css file object.
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

		// import the Theme+ master class
		$this->import('ThemePlus');
	}


	/**
	 * @see CssFile::setMedia
	 *
	 * @param string $strMedia
	 */
	public function setMedia($strMedia)
	{
		$this->strMedia = $strMedia;
	}


	/**
	 * @see CssFile::getMedia
	 * @return string
	 */
	public function getMedia()
	{
		return $this->strMedia;
	}


	/**
	 * Set if urls should be absolutized.
	 *
	 * @param bool $objAbsolutizePage
	 *
	 * @return void
	 */
	public function setAbsolutizePage($objAbsolutizePage)
	{
		$this->objAbsolutizePage = $objAbsolutizePage;
	}


	/**
	 * Get if urls should be absolutized.
	 *
	 * @return bool
	 */
	public function getAbsolutizePage()
	{
		return $this->objAbsolutizePage;
	}


	/**
	 * @see LocalThemePlusFile::getFile
	 * @return string
	 */
	public function getFile()
	{
		if ($this->strProcessedFile == null) {
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
				$strContent = $this->CssUrlRemapper->remapCode($strContent, $this->strOriginFile, $strTemp, $this->objAbsolutizePage ? true : false, $this->objAbsolutizePage);

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

		return $this->strProcessedFile;
	}


	/**
	 * @see ThemePlusFile::getGlobalVariableCode
	 * @return string
	 */
	public function getGlobalVariableCode()
	{
		return $this->getFile() . (strlen($this->strMedia) ? '|' . $this->strMedia : '') . (strlen($this->strCc) ? '|' . $this->strCc : '');
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

		// handle @charset
		$strContent = $this->ThemePlus->handleCharset($strContent);

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<style' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . '>' . $strContent . '</style>');
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
		return $this->getDebugComment() . $this->wrapCc('<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') . ' rel="stylesheet" href="' . TL_SCRIPT_URL . specialchars($strFile) . '" />');
	}


	/**
	 * Convert into a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getOriginFile() . '|' . $this->getMedia() . '|' . $this->getCc();
	}
}
