<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Layout Additional Sources
 * Copyright (C) 2011 Tristan Lins
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
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    LGPL
 * @filesource
 */


/**
 * Class CompilerBase
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class CompilerBase extends Backend
{
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
	 * Detect gzip data end decode it.
	 * 
	 * @param mixed $varData
	 */
	public function decompressGzip($varData) {
		if (	$varData[0] == 31
			&&	$varData[0] == 139
			&&	$varData[0] == 8) {
			return gzdecode($varData);
		} else {
			return $varData;
		}
	}
	
	
	/**
	 * Handle @charset and remove the rule.
	 */
	public function handleCharset($strContent)
	{
		if (preg_match('#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui', $strContent, $arrMatch))
		{
			// convert character encoding to utf-8
			if (strtoupper($arrMatch[1]) != 'UTF-8')
			{
				$strContent = iconv(strtoupper($arrMatch[1]), 'UTF-8', $strContent);
			}
			// remove all @charset rules
			$strContent = preg_replace('#\@charset\s+.*\;#Ui', '', $strContent);
		}
		return $strContent;
	}
}

?>