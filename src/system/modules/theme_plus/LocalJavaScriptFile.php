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
 * Class LocalJavaScriptFile
 */
class LocalJavaScriptFile extends LocalThemePlusFile implements JavaScriptFile
{
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
			$strTemp = $objFile->basename
				. '-' . $objFile->mtime
				. '-' . $strJsMinimizer
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

?>