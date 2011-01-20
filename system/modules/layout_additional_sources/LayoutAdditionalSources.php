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
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Class LayoutAdditionalSources
 * 
 * Adding additional sources to the page layout.
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LayoutAdditionalSources extends Frontend
{
	public function __construct() {
		$this->import('Database');
		$this->import('DomainLink');
		
		switch ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'])
		{
		case 'less':
		case 'less+yui':
			$this->import('LessCssCompiler', 'CssCompiler');
			break;
			
		default:
			$this->import('DefaultCssCompiler', 'CssCompiler');
		}
		
		$this->import('DefaultJsCompiler', 'JsCompiler');
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
		$blnUserLoggedIn = $this->getBELoginStatus();
		
		$arrSourcesMap = array
		(
			'css' => array('-' => array()),
			'js' => array('-' => array())
		);
		
		// remap css and js files from $arrSourcesMap to $arrSources, combine files if possible
		$arrSources = array
		(
			'css' => array(),
			'js' => array()
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
			case 'css_url':
				if (	$GLOBALS['TL_CONFIG']['additional_sources_combination'] != 'combine_all'
					||	!$blnMinimizeCss)
				{
					$arrSources['css'][] = array
					(
						'src'      => $strSource,
						'cc'       => $strCc != '-' ? $strCc : '',
						'external' => true
					);
					continue;
				}
			case 'css_file':
				$strGroup = 'css';
				break;
			
			case 'js_url':
				if (	$GLOBALS['TL_CONFIG']['additional_sources_combination'] != 'combine_all'
					||	!$blnMinimizeJs)
				{
					$arrSources['js'][] = array
					(
						'src'      => $strSource,
						'cc'       => $strCc != '-' ? $strCc : '',
						'external' => true
					);
					continue;
				}
			case 'js_file':
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
		
		// handle css files
		if (count($arrSourcesMap['css']))
		{
			$this->CssCompiler->compile($arrSourcesMap['css'], $arrSources['css'], $blnUserLoggedIn);
		}
		
		// handle js file
		if (count($arrSourcesMap['js']))
		{
			$this->JsCompiler->compile($arrSourcesMap['js'], $arrSources['js'], $blnUserLoggedIn);
		}
		
		return $arrSources;
	}
	
	
	/**
	 * Get the BE login status, do not care of preview mode.
	 * 
	 * @return boolean
	 */
	protected function getBELoginStatus()
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
		$arrHtml = $this->generateInsertHtml($arrLayoutAdditionalSources);
		foreach ($arrHtml as $strHtml)
		{
			$GLOBALS['TL_HEAD'][] = $strHtml;
		}
	}
	
	
	/**
	 * Hook
	 * 
	 * @param string $strTag
	 * @return mixed
	 */
	public function hookReplaceInsertTags($strTag)
	{
		$arrParts = explode('::', $strTag);
		switch ($arrParts[0])
		{
		case 'insert_additional_sources':
			return implode("\n", $this->generateInsertHtml(explode(',', $arrParts[1]))) . "\n";
			
		case 'include_additional_sources':
			return implode("\n", $this->generateIncludeHtml(explode(',', $arrParts[1]))) . "\n";
		}
		 
		return false;
	}
	
	
	/**
	 * Generate the html code.
	 * 
	 * @param array $arrLayoutAdditionalSources
	 * @return array
	 */
	protected function generateInsertHtml($arrLayoutAdditionalSources)
	{
		$arrResult = array();
		if (count($arrLayoutAdditionalSources))
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
					$arrResult[] = $strAdditionalSource;
				}
			}
		}
		return $arrResult;
	}
	
	
	/**
	 * Generate the html code.
	 * 
	 * @param array $arrLayoutAdditionalSources
	 * @return array
	 */
	protected function generateIncludeHtml($arrLayoutAdditionalSources)
	{
		$arrResult = array();
		if (count($arrLayoutAdditionalSources))
		{
			$arrArrAdditionalSources = $this->getSources($arrLayoutAdditionalSources, false, false);
			foreach ($arrArrAdditionalSources as $strType => $arrAdditionalSources)
			{
				foreach ($arrAdditionalSources as $arrAdditionalSource)
				{
					switch ($strType)
					{
					case 'css':
						if ($arrAdditionalSource['external'])
						{
							$strAdditionalSource = sprintf('<link type="text/css" rel="stylesheet" href="%s" />', $arrAdditionalSource['src']);
						}
						else
						{
							$objFile = new File($arrAdditionalSource['src']);
							$strContent = "\n" . $objFile->getContent() . "\n";
							if (!strlen($strCc))
							{
								$strContent = "\n<!--/*--><![CDATA[/*><!--*/" . $strContent . "/*]]>*/-->\n";
							}
							$strAdditionalSource = sprintf("<style type=\"text/css\">%s</style>", $strContent);
						}
						break;
					
					case 'js':
						if ($arrAdditionalSource['external'])
						{
							$strAdditionalSource = sprintf('<script type="text/javascript" src="%s"></script>', $arrAdditionalSource['src']);
						}
						else
						{
							$objFile = new File($arrAdditionalSource['src']);
							$strContent = "\n" . $objFile->getContent() . "\n";
							if (!strlen($strCc))
							{
								$strContent = "\n<!--//--><![CDATA[//><!--" . $strContent . "//--><!]]>\n";
							}
							$strAdditionalSource = sprintf("<script type=\"text/javascript\">%s</script>", $strContent);
						}
						break;
					}
					
					// add the conditional comment
					if (strlen($arrAdditionalSource['cc']))
					{
						$strAdditionalSource = '<!--[' . $arrAdditionalSource['cc'] . ']>' . $strAdditionalSource . '<![endif]-->';
					}
				
					// add the html to the layout head
					$arrResult[] = $strAdditionalSource;
				}
			}
		}
		return $arrResult;
	}
}

?>