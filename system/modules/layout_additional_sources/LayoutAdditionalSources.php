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
	 * Get the resource path from the database result.
	 * If necessary compress the resource and return the compressed resource path.
	 * 
	 * @param Database_Result $objAdditionalSources
	 * @throws Exception Throws Exception if compression failed.
	 * @return The resource path.
	 * 
	 * @deprecated
	 */
	public static function getSource(Database_Result &$objAdditionalSources, $blnAllowGzip = true)
	{
		$blnAcceptGzip = false;
		$arrAcceptEncoding = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']));
		if (in_array('gzip', $arrAcceptEncoding))
		{
			$blnAcceptGzip = true;
		}
		
		// type of the source
		$strType = $objAdditionalSources->type;
		// uri of the source
		$strSrc = $objAdditionalSources->$strType;
		
		if (	$GLOBALS['TL_CONFIG']['additional_sources_compression'] == 'always'
			||  $GLOBALS['TL_CONFIG']['additional_sources_compression'] == 'no_be_user'
			&&  (!BE_USER_LOGGED_IN && TL_MODE == 'FE' || TL_MODE == 'BE'))
		{
			// yui compression
			if (	$objAdditionalSources->compress_yui
				&&  (	$strType == 'js_file'
					||  $strType == 'css_file'))
			{
				$strTarget = preg_replace('#\.(js|css)$#', '.yui.$1', $strSrc);
				// alternative output directory
				if (strlen($objAdditionalSources->compress_outdir))
				{
					// create the outdir if not exists
					if (!is_dir(TL_ROOT . '/' . $objAdditionalSources->compress_outdir))
					{
						mkdir(TL_ROOT . '/' . $objAdditionalSources->compress_outdir, 0777, true);
					}
					$strTarget = $objAdditionalSources->compress_outdir . '/' . basename($strTarget);
				}
				if (	!file_exists($strTarget)
					||  filemtime($strTarget) < filemtime($strSrc))
				{
					$strCmd = sprintf("%s -o %s %s",
						escapeshellcmd($GLOBALS['TL_CONFIG']['yui_cmd']),
						escapeshellarg(TL_ROOT . '/' . $strTarget),
						escapeshellarg(TL_ROOT . '/' . $strSrc));
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
						throw new Exception(sprintf("Execution of yui compressor failed!\nstdout: %s\nstderr: %s", $strOut, $strErr));
					}
				}
				$strSrc = $strTarget;
			}
			
			// gz compression
			if (	$objAdditionalSources->compress_gz
				&&  $blnAcceptGzip
				&&  $blnAllowGzip
				&&  (	$strType == 'js_file'
					||  $strType == 'css_file'))
			{
				$strTarget = preg_replace('#\.(js|css)$#', '.gz.$1', $strSrc);
				// alternative output directory
				if (strlen($objAdditionalSources->compress_outdir))
				{
					// create the outdir if not exists
					if (!is_dir(TL_ROOT . '/' . $objAdditionalSources->compress_outdir))
					{
						mkdir(TL_ROOT . '/' . $objAdditionalSources->compress_outdir, 0777, true);
					}
					$strTarget = $objAdditionalSources->compress_outdir . '/' . basename($strTarget);
				}
				if (	!file_exists($strTarget)
					||  filemtime($strTarget) < filemtime($strSrc))
				{
					$fileSrc = new File($strSrc);
					$fileTarget = new File($strTarget);
					// write gzip-encoded source data to target file
					if (!$fileTarget->write(gzencode($fileSrc->getContent())))
					{
						throw new Exception(sprintf("GZ Compression of %s to %s failed!", $strTarget));
					}
					unset($fileSrc, $fileTarget);
				}
				
				$strSrc = $strTarget;
			}
		}
		
		return $strSrc;
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
		foreach ($arrAdditionalSources as $arrCssSource)
		{
			$strSource = $arrCssSource[$arrCssSource['type']];
			switch ($arrCssSource['type'])
			{
			case 'css_file':
			case 'js_file':
				$objFile = new File($strSource);
				$strKey .= '.' . $arrCssSource['id'] . ':' . $objFile->mtime;
				break;
				
			case 'css_url':
			case 'js_url':
				$strKey .= '.' . $arrCssSource['id'];
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
	public function getSources($arrIds, $blnAllowGzip = true, $blnAddCharset = true, $blnAbsolutizeUrls = false)
	{
		$blnAcceptGzip = false;
		$arrAcceptEncoding = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']));
		if (in_array('gzip', $arrAcceptEncoding))
		{
			$blnAcceptGzip = true;
		}
		
		$arrSourcesMap = array
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
						
						// remap url() entries
						$strRemappingPath = $this->calculateRemappingPath($arrSource['css_file'], $strFile);
						$objUrlRemapper = new UrlRemapper($strRemappingPath);
						$strContent = preg_replace_callback('#url\(["\']?(.*)["\']?\)#U', array(&$objUrlRemapper, 'replace'), $strContent);
					
						// minify
						if ($arrSource['compress_yui'])
						{
							$strContent = $this->compressYui($strContent, 'css');
						}
						
						// add media definition
						$arrMedia = deserialize($arrSource['media'], true);
						if (count($arrMedia))
						{
							$strContent = sprintf('@media %s{%s}', implode(',', $arrMedia), $strContent);
						}
						
						$strCss .= trim($strContent) . "\n";
						break;
					
					case 'css_url':
						$strContent = file_get_contents($this->DomainLink->absolutizeUrl($arrSource['css_url'])) . "\n";
						
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
						
						// minify
						if ($arrSource['compress_yui'])
						{
							$strContent = $this->compressYui($strContent, 'css');
						}
						
						$strCss .= trim($strContent) . "\n";
						break;
					}
				}
				
				if ($blnAddCharset)
				{
					$strCss = '@charset "UTF-8";' . "\n" . $strCss;
				}
				
				file_put_contents(TL_ROOT . '/' . $strFile, $strCss);
				$this->compressFileGzip($strFile, $strFileGz);
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
						
						// minify
						if ($arrSource['compress_yui'])
						{
							$strContent = $this->compressYui($strContent, 'js');
						}
						
						$strJs .= $strContent . "\n";
						break;
					
					case 'js_url':
						$strContent = file_get_contents($this->DomainLink->absolutizeUrl($arrSource['js_url']));
						
						// minify
						if ($arrSource['compress_yui'])
						{
							$strContent = $this->compressYui($strContent, 'js');
						}
						
						$strJs .= $strContent . "\n";
						break;
					}
				}
				
				file_put_contents(TL_ROOT . '/' . $strFile, $strJs);
				$this->compressFileGzip($strFile, $strFileGz);
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
class UrlRemapper {
	private $strRelativePath;
	
	public function __construct($strRelativePath)
	{
		$this->strRelativePath = $strRelativePath;
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
			return 'url(' . $this->strRelativePath . '/' . $arrMatch[1] . ')';
		}
		return $arrMatch[0];
	}
}
?>