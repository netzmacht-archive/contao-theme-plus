<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


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