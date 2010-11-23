<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Class LayoutAdditionalSources
 * 
 * Adding additional sources to the page layout.
 * 
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LayoutAdditionalSources extends Frontend
{
	public function __construct() {
		$this->import('Database');
		$this->import('DomainLink');
	}
	
	
	/**
	 * Calculate a temporary combined file name. 
	 * 
	 * @param string $strGroup
	 * @param string $strCc
	 * @param array $arrAdditionalSources
	 * @return string
	 */
	protected function calculateTempFile($strGroup, $strCc, $arrAdditionalSources)
	{
		$strKey = $strCc;
		foreach ($arrAdditionalSources as $arrSource)
		{
			$strSource = $arrSource[$arrSource['type']];
			switch ($arrSource['type'])
			{
			case 'css_file':
			case 'js_file':
				$objFile = new File($strSource);
				$strKey .= '.' . $arrSource['id'] . ':' . $objFile->mtime;
				break;
				
			case 'css_url':
			case 'js_url':
				$strRealPath = $arrSource[$arrSource['type'].'_real_path'];
				if ($strRealPath)
				{
					$objFile = new File($strRealPath);
					$strKey .= '.' . $arrSource['id'] . ':' . $objFile->mtime;
				}
				else
				{
					$strKey .= '.' . $arrSource['id'];
				}
				break;
			}
		}
		switch ($strGroup)
		{
		case 'css':
			$strPrefix = 'stylesheet';
			$strExtension = 'css';
			break;
			
		case 'js':
			$strPrefix = 'javascript';
			$strExtension = 'js';
			break;
		}
		return 'system/html/' . $strPrefix . '.' . substr(md5($strKey), 0, 8) . '.' . $strExtension;
	}
	
	
	/**
	 * Calculate a remapped url prefix.
	 * 
	 * @param string $strSourceFile
	 * @param string $strTargetFile
	 * @return string
	 */
	protected function calculateRemappingPath($strSourceFile, $strTargetFile)
	{
		$strSourceDir = dirname($strSourceFile);
		$strTargetDir = dirname($strTargetFile);
		$strRelativePath = '';
		while ($strTargetDir && $strTargetDir != '.' && strpos($strSourceDir . '/', $strTargetDir . '/') !== 0)
		{
			$strRelativePath .= '../';
			$strTargetDir = dirname($strTargetDir);
		}
		return $strRelativePath . ($strTargetDir != '.' ? substr($strSourceDir, strlen($strTargetDir) + 1) : $strSourceDir);
	}
	
	
	/**
	 * Compress the content with yui compressor.
	 * 
	 * @param string $strContent
	 * @param string $strType
	 * @return string
	 * @throws Exception
	 */
	public function compressYui($strContent, $strType)
	{
		$strCmd = escapeshellcmd($GLOBALS['TL_CONFIG']['yui_cmd']) . ' --type ' . escapeshellarg($strType) . ' --charset utf8';
		// execute yui-compressor
		$procYUI = proc_open(
			$strCmd,
			array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w")
			),
			$arrPipes);
		if ($procYUI === false)
		{
			throw new Exception(sprintf("yui compressor could not be started!"));
		}
		// write contents
		fwrite($arrPipes[0], $strContent);
		// close stdin
		fclose($arrPipes[0]);
		// read and close stdout
		$strOut = stream_get_contents($arrPipes[1]);
		fclose($arrPipes[1]);
		// read and close stderr
		$strErr = stream_get_contents($arrPipes[2]);
		fclose($arrPipes[2]);
		// wait until yui-compressor terminates
		$intCode = proc_close($procYUI);
		if ($intCode != 0)
		{
			throw new Exception(sprintf("Execution of yui compressor failed!\ncmd: %s\nstdout: %s\nstderr: %s", $strCmd, $strOut));
		}
		return $strOut;
	}
	
	
	/**
	 * Compress a source file with gzip and save it as the target file.
	 * 
	 * @param string $strSrc
	 * @param string $strTarget
	 * @throws Exception
	 */
	public function compressFileGzip($strSrc, $strTarget)
	{
		$fileSrc = new File($strSrc);
		$fileTarget = new File($strTarget);
		// write gzip-encoded source data to target file
		if (!$fileTarget->write(gzencode($fileSrc->getContent())))
		{
			throw new Exception(sprintf("GZ Compression of %s to %s failed!", $strTarget));
		}
	}
	
	/**
	 * Get the sources from ids, combine them and return an array of it.
	 * 
	 * @param array
	 * @param boolean
	 * @param boolean
	 * @param boolean
	 * @return array
	 */
	public function getSources($arrIds, $blnAllowGzip = true, $blnAddCharset = true, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$blnAcceptGzip = false;
		$arrAcceptEncoding = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']));
		if (!$GLOBALS['TL_CONFIG']['gz_compression_disabled'] && in_array('gzip', $arrAcceptEncoding))
		{
			$blnAcceptGzip = true;
		}
		
		$arrSourcesMap = array
		(
			'css' => array('-' => array()),
			'js' => array('-' => array())
		);
		
		// collect css and js files into $arrSourcesMap, depending of the conditional comment
		$objAdditionalSources = $this->Database->execute("
				SELECT
					*
				FROM
					`tl_additional_source`
				WHERE
					`id` IN (" . implode(',', array_map('intval', $arrIds)) . ")
				ORDER BY
					`sorting`");
		while ($objAdditionalSources->next())
		{
			$strType = $objAdditionalSources->type;
			$strCc = $objAdditionalSources->cc ? $objAdditionalSources->cc : '-';
			$strSource = $objAdditionalSources->$strType;
			switch ($strType)
			{
			case 'css_file':
			case 'css_url':
				$strGroup = 'css';
				break;
			
			case 'js_file':
			case 'js_url':
				$strGroup = 'js';
				break;
			
			default:
				continue;
			}
			
			if (!isset($arrSourcesMap[$strGroup][$strCc]))
			{
				$arrSourcesMap[$strGroup][$strCc] = array();
			}
			$arrSourcesMap[$strGroup][$strCc][] = $objAdditionalSources->row();
		}
		
		// remap css and js files from $arrSourcesMap to $arrSources, combine files if possible
		$arrSources = array
		(
			'css' => array(),
			'js' => array()
		);
		
		// handle css files
		foreach ($arrSourcesMap['css'] as $strCc => $arrCssSources)
		{
			$strFile = $this->calculateTempFile('css', $strCc, $arrCssSources);
			$strFileGz = preg_replace('#\.(js|css)$#', '.gz.$1', $strFile);
			if (!file_exists(TL_ROOT . '/' . $strFile))
			{
				$strCss = '';
				foreach ($arrCssSources as $arrSource)
				{
					switch ($arrSource['type'])
					{
					case 'css_file':
						$objFile = new File($arrSource['css_file']);
						$strContent = $objFile->getContent();
						
						// handle @charset
						if (preg_match('#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui', $strContent, $arrMatch))
						{
							// convert character encoding to utf-8
							if (strtoupper($arrMatch[1]) != 'UTF-8')
							{
								$strContent = iconv(strtoupper($arrMatch[1]), 'UTF-8', $strContent);
							}
							// remove @charset tag
							$strContent = str_replace($arrMatch[0], '', $strContent);
						}
						
						// remap url(..) entries
						if ($blnAbsolutizeUrls)
						{
							$strRemappingPath = dirname($arrSource['css_file']);
						}
						else
						{
							$strRemappingPath = $this->calculateRemappingPath($arrSource['css_file'], $strFile);
						}
						$objUrlRemapper = new UrlRemapper($strRemappingPath, $blnAbsolutizeUrls, $objAbsolutizePage);
						$strContent = preg_replace_callback('#url\(["\']?(.*)["\']?\)#U', array(&$objUrlRemapper, 'replace'), $strContent);
					
						// add media definition
						$arrMedia = deserialize($arrSource['media'], true);
						if (count($arrMedia))
						{
							$strContent = sprintf('@media %s{%s}', implode(',', $arrMedia), $strContent);
						}
						
						$strCss .= trim($strContent) . "\n";
						break;
					
					case 'css_url':
						if ($arrSource['css_url_real_path'])
						{
							$strContent = file_get_contents($arrSource['css_url_real_path']);
						}
						else
						{
							$strContent = file_get_contents($this->DomainLink->absolutizeUrl($arrSource['css_url'])) . "\n";
						}
						
						// handle @charset
						if (preg_match('#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui', $strContent, $arrMatch))
						{
							// convert character encoding to utf-8
							if (strtoupper($arrMatch[1]) != 'UTF-8')
							{
								$strContent = iconv(strtoupper($arrMatch[1]), 'UTF-8', $strContent);
							}
							// remove @charset tag
							$strContent = str_replace($arrMatch[0], '', $strContent);
						}
						
						$strCss .= trim($strContent) . "\n";
						break;
					}
				}
		
				// add charset definition
				if ($blnAddCharset)
				{
					$strCss = '@charset "UTF-8";' . "\n" . $strCss;
				}
				
				// minify
				if (!$GLOBALS['TL_CONFIG']['yui_compression_disabled'])
				{
					$strCss = $this->compressYui($strCss, 'css');
				}
				
				// store the temporary file
				file_put_contents(TL_ROOT . '/' . $strFile, $strCss);
				
				// always create the gzip compressed version
				if (!$GLOBALS['TL_CONFIG']['gz_compression_disabled'])
				{
					$this->compressFileGzip($strFile, $strFileGz);
				}
			}
			
			if ($blnAcceptGzip && $blnAllowGzip)
			{
				$strFile = $strFileGz;
			}
			
			if (!isset($arrSources['css']))
			{
				$arrSources['css'] = array();
			}
			$arrSources['css'][] = array
			(
				'src' => $strFile,
				'cc' => $strCc != '-' ? $strCc : ''
			);
		}
		
		// handle js file
		foreach ($arrSourcesMap['js'] as $strCc => $arrJsSources)
		{	
			$strFile = $this->calculateTempFile('js', $strCc, $arrSourcesMap['js'][$strCc]);
			$strFileGz = preg_replace('#\.(js|css)$#', '.gz.$1', $strFile);
			if (!file_exists(TL_ROOT . '/' . $strFile))
			{
				$strJs = '';
				foreach ($arrJsSources as $arrSource)
				{
					switch ($arrSource['type'])
					{
					case 'js_file':
						$objFile = new File($arrSource['js_file']);
						$strContent = $objFile->getContent();
						
						$strJs .= $strContent . "\n";
						break;
					
					case 'js_url':
						if ($arrSource['js_url_real_path'])
						{
							$strContent = file_get_contents($arrSource['js_url_real_path']);
						}
						else
						{
							$strContent = file_get_contents($this->DomainLink->absolutizeUrl($arrSource['js_url']));
						}
												
						$strJs .= $strContent . "\n";
						break;
					}
				}
				
				// minify
				if (!$GLOBALS['TL_CONFIG']['yui_compression_disabled'])
				{
					$strJs = $this->compressYui($strJs, 'js');
				}
				
				// store the temporary file
				file_put_contents(TL_ROOT . '/' . $strFile, $strJs);
				
				// always create the gzip compressed version
				if (!$GLOBALS['TL_CONFIG']['gz_compression_disabled'])
				{
					$this->compressFileGzip($strFile, $strFileGz);
				}
			}
			
			if ($blnAcceptGzip && $blnAllowGzip)
			{
				$strFile = $strFileGz;
			}
			
			if (!isset($arrSources['js']))
			{
				$arrSources['js'] = array();
			}
			$arrSources['js'][] = array
			(
				'src' => $strFile,
				'cc' => $strCc != '-' ? $strCc : ''
			);
		}
		
		return $arrSources;
	}
	
	
	/**
	 * Get the BE login status, do not care of preview mode.
	 * 
	 * @return boolean
	 */
	private function getBELoginStatus()
	{
		$this->import('Input');
		$this->import('Environment');
		
		$strCookie = 'BE_USER_AUTH';
		
		$hash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $this->Environment->ip : '') . $strCookie);
		
		// Validate the cookie hash
		if ($this->Input->cookie($strCookie) == $hash)
		{
			// Try to find the session
			$objSession = $this->Database->prepare("SELECT * FROM tl_session WHERE hash=? AND name=?")
										 ->limit(1)
										 ->execute($hash, $strCookie);

			// Validate the session ID and timeout
			if ($objSession->numRows && $objSession->sessionID == session_id() && ($GLOBALS['TL_CONFIG']['disableIpCheck'] || $objSession->ip == $this->Environment->ip) && ($objSession->tstamp + $GLOBALS['TL_CONFIG']['sessionTimeout']) > time())
			{
				// The session could be verified
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Hook
	 * 
	 * @param Database_Result $objPage
	 * @param Database_Result $objLayout
	 * @param PageRegular $objPageRegular
	 */
	public function generatePage(Database_Result $objPage, Database_Result $objLayout, PageRegular $objPageRegular)
	{
		$arrLayoutAdditionalSources = deserialize($objLayout->additional_source, true);
		
		// check if a BE user is logged in
		// include the original source files
		if ($this->getBELoginStatus())
		{
			$arrArrAdditionalSources = array
			(
				'css' => array(),
				'js' => array()
			);
			
			$objAdditionalSources = $this->Database->execute("
					SELECT
						*
					FROM
						`tl_additional_source`
					WHERE
						`id` IN (" . implode(',', array_map('intval', $arrLayoutAdditionalSources)) . ")
					ORDER BY
						`sorting`");
			while ($objAdditionalSources->next())
			{
				$strType = $objAdditionalSources->type;
				$strCc = $objAdditionalSources->cc ? $objAdditionalSources->cc : '';
				$strSource = $objAdditionalSources->$strType;
				
				switch ($strType)
				{
				case 'css_file':
				case 'css_url':
					$arrMedia = deserialize($objAdditionalSources->media, true);
					$strAdditionalSource = sprintf('<link type="text/css" rel="stylesheet" href="%s"%s />', $strSource, count($arrMedia) ? ' media="' . implode(',', $arrMedia) . '"' : '');
					break;
					
				case 'js_file':
				case 'js_url':
					$strAdditionalSource = sprintf('<script type="text/javascript" src="%s"></script>', $strSource);
				}
				
				// add the conditional comment
				if (strlen($strCc))
				{
					$strAdditionalSource = '<!--[' . $strCc . ']>' . $strAdditionalSource . '<![endif]-->';
				}
			
				// add the html to the layout head
				$GLOBALS['TL_HEAD'][] = $strAdditionalSource;
			}
		}
		
		// use reduced files
		else
		{
			$arrArrAdditionalSources = $this->getSources($arrLayoutAdditionalSources);
			foreach ($arrArrAdditionalSources as $strType => $arrAdditionalSources)
			{
				foreach ($arrAdditionalSources as $arrAdditionalSource)
				{
					switch ($strType)
					{
					case 'css':
						$strAdditionalSource = sprintf('<link type="text/css" rel="stylesheet" href="%s" />', $arrAdditionalSource['src']);
						break;
					
					case 'js':
						$strAdditionalSource = sprintf('<script type="text/javascript" src="%s"></script>', $arrAdditionalSource['src']);
						break;
					}
					
					// add the conditional comment
					if (strlen($arrAdditionalSource['cc']))
					{
						$strAdditionalSource = '<!--[' . $arrAdditionalSource['cc'] . ']>' . $strAdditionalSource . '<![endif]-->';
					}
				
					// add the html to the layout head
					$GLOBALS['TL_HEAD'][] = $strAdditionalSource;
				}
			}
		}
	}
}

/**
 * Class UrlRemapper
 * 
 * Callback class for preg_replace_callback.
 * 
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class UrlRemapper extends Controller {
	private $strRelativePath;
	private $blnAbsolutizeUrls;
	private $objAbsolutizePage;
	
	public function __construct($strRelativePath, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$this->import('DomainLink');
		$this->strRelativePath = $strRelativePath;
		$this->blnAbsolutizeUrls = $blnAbsolutizeUrls;
		$this->objAbsolutizePage = $objAbsolutizePage;
	}
	
	public function replace($arrMatch)
	{
		if (!preg_match('#^\w+://#', $arrMatch[1]) && $arrMatch[1][0] != '/')
		{
			$strPath = $this->strRelativePath;
			$strUrl = $arrMatch[1];
			while (preg_match('#^\.\./#', $strUrl))
			{
				$strPath = dirname($strPath);
				$strUrl = substr($strUrl, 3);
			}
			$strUrl = $strPath . '/' . $strUrl;
			if ($this->blnAbsolutizeUrls)
			{
				$strUrl = $this->DomainLink->absolutizeUrl($strUrl, $this->objAbsolutizePage);
			}
			return 'url(' . $strUrl . ')';
		}
		return $arrMatch[0];
	}
}
?>