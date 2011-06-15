<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ThemePlus
 * 
 * Adding additional sources to the page layout.
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class ThemePlus extends Frontend
{
	/**
	 * If is in live mode.
	 */
	private static $blnLiveMode = false;
	
	
	/**
	 * Cached be login status.
	 */
	private static $blnBeLoginStatus = null;
	
	
	/**
	 * Set productive mode.
	 */
	public static function setLiveMode()
	{
		self::$blnLiveMode = true;
	}
	
	
	/**
	 * Set designer mode.
	 */
	public static function setDesignerMode()
	{
		self::$blnLiveMode = false;
	}
	
	
	/**
	 * Get the BE login status, do not care of preview mode.
	 * If the BE login status is true, the page cache is disabled!
	 * 
	 * @return boolean
	 */
	public static function getBELoginStatus()
	{
		if (self::$blnLiveMode)
		{
			return false;
		}
		
		if (self::$blnBeLoginStatus == null)
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
					
					// disable cache of the page
					global $objPage;
					if ($objPage)
					{
						$objPage->cache = 0;
					}
					
					return (self::$blnBeLoginStatus = true);
				}
			}
			
			return (self::$blnBeLoginStatus = false);
		}

		return self::$blnBeLoginStatus;
	}
	
	
	/**
	 * Detect gzip data end decode it.
	 * 
	 * @param mixed $varData
	 */
	public static function decompressGzip($varData) {
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
	public static function handleCharset($strContent)
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
	
	
	/**
	 * Ignore the be logged in state.
	 * 
	 * @var bool
	 */
	protected $blnIgnoreLogin = false;
	
	
	public function __construct() {
		$this->import('Database');
	}
	
	
	/**
	 * setter
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
		default:
			$this->$strKey = $varValue;
		}
	}
	
	
	/**
	 * Detect page aggregate.
	 */
	public function getPageLayoutAggregateState()
	{
		if ($blnAggregate == null && $GLOBALS['objPage'])
		{
			$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE id=?")
				->execute($GLOBALS['objPage']->layout);
			if ($objLayout->next())
			{
				return $objLayout->aggregate ? true : false;
			}
		}
		return true;
	}	
	
	/**
	 * Aggregate css files.
	 */
	public function aggregateCssFiles($arrFiles)
	{
		$arrAggregatedFiles = array();
		$objAggregator = null;
		
		// walk over all files
		foreach ($arrFiles as $objFile)
		{
			// if file is a local css file and it can be aggregated, combine them
			if ($objFile instanceof LocalCssFile && $objFile->isAggregateable())
			{
				// if there is no aggregator, create one
				if ($objAggregator == null)
				{
					$objAggregator = new CssFileAggregator();
					$arrAggregatedFiles[] = $objAggregator;
				}
				
				// add file to aggregator
				$objAggregator->add($objFile);
			}
			
			// the file can not be aggregated
			else
			{
				// if there is an aggregator, empty the variable
				if ($objAggregator != null)
				{
					$objAggregator = null;
				}
				
				// add the not aggregateable file
				$arrAggregatedFiles[] = $objFile;
			}
		}
		
		return $arrAggregatedFiles;
	}
	
	
	/**
	 * Get css files by ids.
	 */
	public function getCssFiles($arrIds = false, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		if ($blnAggregate == null && $GLOBALS['objPage'])
		{
			$blnAggregate = $this->getPageLayoutAggregateState();
		}
		
		$arrStylesheets = array();
		
		// collect css and js files into $arrSourcesMap, depending of the conditional comment
		$objFile = $this->Database->execute("
				SELECT
					*
				FROM
					tl_theme_plus_file
				WHERE
					id IN (" . implode(',', array_map('intval', $arrIds)) . ")
				AND
					(type = 'less_url' OR type = 'less_file' OR type = 'css_url' OR type = 'css_file')
				ORDER BY
					sorting");
		while ($objFile->next())
		{
			$strType = $objFile->type;
			$strValue = $objFile->$strType;
			switch ($strType)
			{
			case 'less_url':
				$arrStylesheets[] = new ExternalLessCssFile($varValue, deserialize($objAdditionalSources->media, true));
				break;
				
			case 'less_file':
				$arrStylesheets[] = new LocalLessCssFile($strValue, deserialize($objAdditionalSources->media, true), $blnAbsolutizeUrls ? $objAbsolutizePage : false);
				break;
				
			case 'css_url':
				$arrStylesheets[] = new ExternalCssFile($strValue, deserialize($objAdditionalSources->media, true));
				break;
				
			case 'css_file':
				$arrStylesheets[] = new LocalCssFile($strValue, deserialize($objAdditionalSources->media, true), $blnAbsolutizeUrls ? $objAbsolutizePage : false);
				break;
			}
		}
		
		// aggregate
		if ($blnAggregate)
		{
			$arrStylesheets = $this->aggregateCssFiles($arrStylesheets);
		}
		
		return $arrStylesheets;
	}
	
	
	/**
	 * Aggregate javascript files.
	 */
	public function aggregateJavaScriptFiles($arrFiles)
	{
		$arrAggregatedFiles = array();
		$objAggregator = null;
		
		// walk over all files
		foreach ($arrFiles as $objFile)
		{
			// if file is a local css file and it can be aggregated, combine them
			if ($objFile instanceof LocalJavaScriptFile && $objFile->isAggregateable())
			{
				// if there is no aggregator, create one
				if ($objAggregator == null)
				{
					$objAggregator = new JavaScriptFileAggregator();
					$arrAggregatedFiles[] = $objAggregator;
				}
				
				// add file to aggregator
				$objAggregator->add($objFile);
			}
			
			// the file can not be aggregated
			else
			{
				// if there is an aggregator, empty the variable
				if ($objAggregator != null)
				{
					$objAggregator = null;
				}
				
				// add the not aggregateable file
				$arrAggregatedFiles[] = $objFile;
			}
		}
		
		return $arrAggregatedFiles;
	}
	
	
	/**
	 * Get javascript files by id.
	 */
	public function getJavaScriptFiles($arrIds, $blnAggregate = null)
	{
		if ($blnAggregate == null && $GLOBALS['objPage'])
		{
			$blnAggregate = $this->getPageLayoutAggregateState();
		}
		
		$arrJavaScripts = array();
		
		// collect css and js files into $arrSourcesMap, depending of the conditional comment
		$objFile = $this->Database->execute("
				SELECT
					*
				FROM
					tl_theme_plus_file
				WHERE
					id IN (" . implode(',', array_map('intval', $arrIds)) . ")
				AND
					(type = 'js_url' OR type = 'js_file')
				ORDER BY
					sorting");
		while ($objFile->next())
		{
			$strType = $objFile->type;
			$strValue = $objFile->$strType;
			switch ($strType)
			{
			case 'js_url':
				$arrJavaScripts[] = new ExternalJavaScriptFile($strValue);
				break;
				
			case 'js_file':
				$arrJavaScripts[] = new LocalJavaScriptFile($strValue);
				break;
			
			default:
				continue;
			}
		}
		
		// aggregate
		if ($blnAggregate)
		{
			$arrJavaScripts = $this->aggregateJavaScriptFiles($arrJavaScripts);
		}
		
		return $arrJavaScripts;
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
		$arrFileIds = array_merge
		(
			deserialize($objLayout->theme_plus_files, true),
			$this->inheritFiles($objPage)
		);
		
		$arrHtml = $this->includeFiles($arrFileIds, $objLayout->aggregate ? true : false);
		foreach ($arrHtml as $strHtml)
		{
			$GLOBALS['TL_HEAD'][] = $strHtml;
		}
	}
	
	
	/**
	 * Inherit additional sources from pages.
	 * 
	 * @param Database_Result $objPage
	 */
	protected function inheritFiles(Database_Result $objPage)
	{
		$arrTemp = deserialize($objPage->theme_plus_files, true);
		if ($objPage->pid > 0)
		{
			$objParentPage = $this->Database->prepare("
					SELECT
						*
					FROM
						tl_page
					WHERE
						id=?")
				->execute($objPage->pid);
			if ($objParentPage->next())
			{
				$arrTemp = array_merge
				(
					$arrTemp,
					$this->inheritFiles($objParentPage)
				);
			}
		}
		return $arrTemp;
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
		case 'include_theme_file':
			return implode("\n", $this->includeFiles(explode(',', $arrParts[1]))) . "\n";
			break;
			
		case 'embed_theme_file':
			return implode("\n", $this->embedFiles(explode(',', $arrParts[1]))) . "\n";
			break;
			
		// @deprecated
		case 'insert_additional_sources':
			return implode("\n", $this->includeFiles(explode(',', $arrParts[1]))) . "\n";
			
		// @deprecated
		case 'include_additional_sources':
			return implode("\n", $this->embedFiles(explode(',', $arrParts[1]))) . "\n";
		}
		 
		return false;
	}
	
	
	/**
	 * Generate the html code.
	 * 
	 * @param array $arrFileIds
	 * @param bool $blnAbsolutizeUrls
	 * @param object $objAbsolutizePage
	 * @return string
	 */
	public function includeFiles($arrFileIds, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$arrResult = array();
		
		// add css files
		$arrFiles = $this->getCssFiles($arrFileIds, $blnAggregate, $blnAbsolutizeUrls, $objAbsolutizePage);
		foreach ($arrFiles as $arrFile)
		{
			$strResult[] = $objFile->getIncludeHtml();
		}
		
		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $arrFile)
		{
			$strResult[] = $objFile->getIncludeHtml();
		}
		return $arrResult;
	}
	
	
	/**
	 * Generate the html code.
	 * 
	 * @param array $arrFileIds
	 * @return array
	 */
	public function embedFiles($arrFileIds, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$arrResult = array();
		
		// add css files
		$arrFiles = $this->getCssFiles($arrFileIds, $blnAbsolutizeUrls, $objAbsolutizePage);
		foreach ($arrFiles as $arrFile)
		{
			$strResult[] = $objFile->getEmbededHtml();
		}
		
		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $arrFile)
		{
			$strResult[] = $objFile->getEmbededHtml();
		}
		return $arrResult;
	}
}

?>