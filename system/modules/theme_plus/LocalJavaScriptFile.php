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
class LocalJavaScriptFile extends LocalThemePlusFile {


	/**
	 * The processed temporary file path.
	 */
	protected $strProcessedFile;


	/**
	 * Create a new javascript file object.
	 */
	public function __construct($strOriginFile, $strCc = '', $objTheme = false)
	{
		parent::__construct($strOriginFile, $strCc, $objTheme);
		$this->strProcessedFile = null;

		// import the Theme+ master class
		$this->import('ThemePlus');
	}


	/**
	 * Get the file path relative to TL_ROOT
	 */
	public function getFile()
	{
		if ($this->strProcessedFile == null)
		{
			$this->import('Compression');

			$strJsMinimizer = $this->ThemePlus->getBELoginStatus() ? false : $this->Compression->getDefaultJsMinimizer();
			if (!$strJsMinimizer)
			{
				$strJsMinimizer = 'none';
			}

			$objFile = new File($this->strOriginFile);
			$strTemp = $objFile->basename
					. '-' . $objFile->mtime
					. '-' . $strJsMinimizer
					. '-' . $this->ThemePlus->getVariablesHashByTheme($this->objTheme);
			$strTemp = sprintf('system/scripts/%s-%s.js', $objFile->filename, substr(md5($strTemp), 0, 8));

			if (!file_exists(TL_ROOT . '/' . $strTemp))
			{
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


	/**
	 * Get embeded html code
	 */
	public function getEmbededHtml()
	{
		global $objPage;

		// get the file
		$strFile = $this->getFile();
		$objFile = new File($strFile);

		// get the css code
		$strContent = $objFile->getContent();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . $strContent . '</script>');
	}


	/**
	 * Get included html code
	 */
	public function getIncludeHtml()
	{
		global $objPage;

		// get the file
		$strFile = $this->getFile();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . TL_SCRIPT_URL . specialchars($strFile) . '"></script>');
	}


	/**
	 * Convert into a string.
	 */
	public function __toString()
	{
		return $this->getOriginFile() . '|' . $this->getCc();
	}
}

?>