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
 * Class JavaScriptFileAggregator
 */
class JavaScriptFileAggregator extends FileAggregator
{
	/**
	 * The files to aggregate.
	 *
	 * @var array
	 */
	protected $arrFiles = array();


	/**
	 * The aggregated file.
	 *
	 * @var string
	 */
	protected $strAggregatedFile = null;


	/**
	 * Create a new css file object.
	 *
	 * @param string $strScope
	 */
	public function __construct($strScope)
	{
		parent::__construct($strScope);
	}


	/**
	 * Add a file.
	 *
	 * @param LocalJavaScriptFile $objFile
	 * @return void
	 */
	public function add(LocalJavaScriptFile $objFile)
	{
		$this->arrFiles[] = $objFile;
	}


	/**
	 * @see LocalThemePlusFile::getFile
	 * @throws Exception
	 * @return string
	 */
	public function getFile()
	{
		if ($this->strAggregatedFile == null)
		{
			$arrFiles = array();
			$strKey = count($this->arrFiles);
			foreach ($this->arrFiles as $objThemePlusFile)
			{
				if ($objThemePlusFile instanceof LocalJavaScriptFile)
				{
					if ($objThemePlusFile->isAggregateable())
					{
						$strFile = $objThemePlusFile->getFile();
						$objFile = new File($strFile);
						$arrFiles[] = $strFile;
						$strKey .= sprintf(':%s-%d', basename($strFile, '.js'), $objFile->mtime);
						continue;
					}
				}
				throw new Exception('Could not aggreagate the file: ' . $objFile);
			}

			$strTemp = 'system/scripts/javascript-' . substr(md5($strKey), 0, 8) . '.js';

			if (!file_exists(TL_ROOT . '/' . $strTemp))
			{
				$this->import('Compression');

				// import the Theme+ master class
				$this->import('ThemePlus');

				// import the gzip compressor
				$strGzipCompressorClass = $this->Compression->getCompressorClass('gzip');
				$this->import($strGzipCompressorClass, 'Compressor');

				// build the content
				$strContent = '';

				foreach ($arrFiles as $strFile)
				{
					$objFile = new File($strFile);

					// get the css code
					$strSubContent = $objFile->getContent();

					// detect and decompress gziped content
					$strSubContent = $this->ThemePlus->decompressGzip($strSubContent);

					// trim content
					$strSubContent = trim($strSubContent);

					// append to content
					if (strlen($strSubContent)>0)
					{
						$strContent .= $strSubContent . "\n";
					}
				}

				// write the file
				$objTemp = new File($strTemp);
				$objTemp->write($strContent);
				$objTemp->close();

				// create the gzip compressed version
				if ($GLOBALS['TL_CONFIG']['gzipScripts'])
				{
					$this->Compressor->compress($strTemp, $strTemp . '.gz');
				}
			}

			$this->strAggregatedFile = $strTemp;
		}
		return $this->strAggregatedFile;
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
		if ($blnLazy)
		{
			return $this->getDebugComment() . '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . $this->ThemePlus->wrapJavaScriptLazyEmbedded($strContent) . '</script>';
		}
		else
		{
			return $this->getDebugComment() . '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . $strContent . '</script>';
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
		if ($blnLazy)
		{
			return $this->getDebugComment() . '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . '>' . $this->ThemePlus->wrapJavaScriptLazyInclude(TL_SCRIPT_URL . $strFile) . '</script>';
		}
		else
		{
			return $this->getDebugComment() . '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . TL_SCRIPT_URL . specialchars($strFile) . '"></script>';
		}
	}
}

?>