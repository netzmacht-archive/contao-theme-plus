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
 * Class ThemePlus
 *
 * Adding files to the page layout.
 */
class ThemePlus extends Frontend
{
	/**
	 * Singleton
	 */
	private static $instance = null;


	/**
	 * Get the singleton instance.
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new ThemePlus();
		}
		return self::$instance;
	}


	/**
	 * Singleton constructor.
	 */
	protected function __construct()
	{
		$this->import('Database');
		$this->import('Environment');
		$this->import('Input');
	}


	/**
	 * If is in live mode.
	 */
	private $blnLiveMode = false;


	/**
	 * Cached be login status.
	 */
	private $blnBeLoginStatus = null;


	/**
	 * The variables cache.
	 */
	private $arrVariables = null;


	/**
	 * Get productive mode status.
	 */
	public function isLiveMode()
	{
		return $this->blnLiveMode ? true : false;
	}


	/**
	 * Set productive mode.
	 */
	public function setLiveMode()
	{
		$this->blnLiveMode = true;
	}


	/**
	 * Get productive mode status.
	 */
	public function isDesignerMode()
	{
		return $this->blnLiveMode ? false : true;
	}


	/**
	 * Set designer mode.
	 */
	public function setDesignerMode()
	{
		$this->blnLiveMode = false;
	}


	/**
	 * Get the BE login status, do not care of preview mode.
	 * If the BE login status is true, the page cache is disabled!
	 *
	 * @return boolean
	 */
	public function getBELoginStatus()
	{
		if ($this->blnLiveMode) {
			return false;
		}

		if ($this->blnBeLoginStatus == null) {
			$objInput       = Input::getInstance();
			$objEnvironment = Environment::getInstance();

			$strCookie = 'BE_USER_AUTH';

			$hash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $objEnvironment->ip : '') . $strCookie);

			// Validate the cookie hash
			if ($objInput->cookie($strCookie) == $hash) {
				// Try to find the session
				$objSession = $this->Database->prepare("SELECT * FROM tl_session WHERE hash=? AND name=?")
					->limit(1)
					->execute($hash, $strCookie);

				// Validate the session ID and timeout
				if ($objSession->numRows && $objSession->sessionID == session_id() && ($GLOBALS['TL_CONFIG']['disableIpCheck'] || $objSession->ip == $objEnvironment->ip) && ($objSession->tstamp + $GLOBALS['TL_CONFIG']['sessionTimeout']) > time()) {
					// The session could be verified

					// disable cache of the page
					global $objPage;
					if ($objPage) {
						$objPage->cache = 0;
					}

					return ($this->blnBeLoginStatus = true);
				}
			}

			return ($this->blnBeLoginStatus = false);
		}

		return $this->blnBeLoginStatus;
	}


	/**
	 * Detect gzip data end decode it.
	 *
	 * @param mixed $varData
	 */
	public function decompressGzip($varData)
	{
		if ($varData[0] == 31
			&& $varData[0] == 139
			&& $varData[0] == 8
		) {
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
		if (preg_match('#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui', $strContent, $arrMatch)) {
			// convert character encoding to utf-8
			if (strtoupper($arrMatch[1]) != 'UTF-8') {
				$strContent = iconv(strtoupper($arrMatch[1]), 'UTF-8', $strContent);
			}
			// remove all @charset rules
			$strContent = preg_replace('#\@charset\s+.*\;#Ui', '', $strContent);
		}
		return $strContent;
	}


	/**
	 * Render a variable to css code.
	 */
	public function renderVariable($varArg, $strPath = false)
	{
		if ($varArg instanceof Database_Result) {
			$arrRow = $varArg->row();
		}
		else
		{
			$arrRow = $varArg;
		}

		// HOOK: create framework code
		if (isset($GLOBALS['TL_HOOKS']['renderVariable']) && is_array($GLOBALS['TL_HOOKS']['renderVariable'])) {
			foreach ($GLOBALS['TL_HOOKS']['renderVariable'] as $callback)
			{
				$this->import($callback[0]);
				$varResult = $this->$callback[0]->$callback[1]($arrRow);
				if ($varResult) {
					return $varResult;
				}
			}
		}

		switch ($arrRow['type'])
		{
			case 'text':
				return $arrRow['text'];

			case 'url':
				return sprintf('url("%s")', str_replace('"', '\\"', $arrRow['url']));

			case 'file':
				if ($strPath) {
					$this->import('CssUrlRemapper');
					$strFile = $this->CssUrlRemapper->calculateRemappingPath($strPath, $arrRow['file']);
				}
				else
				{
					$strFile = $arrRow['file'];
				}
				return sprintf('url("%s")', str_replace('"', '\\"', $strFile));

			case 'color':
				return '#' . $arrRow['color'];

			case 'size':
				$arrSize       = deserialize($arrRow['size']);
				$arrTargetSize = array();
				foreach (array('top', 'right', 'bottom', 'left') as $k)
				{
					if (strlen($arrSize[$k])) {
						$arrTargetSize[] = $arrSize[$k] . $arrSize['unit'];
					}
					else
					{
						$arrTargetSize[] = '';
					}
				}
				while (count($arrTargetSize) > 0 && empty($arrTargetSize[count($arrTargetSize) - 1]))
				{
					array_pop($arrTargetSize);
				}
				foreach ($arrTargetSize as $k=> $v)
				{
					if (empty($v)) {
						$arrTargetSize[$k] = '0';
					}
				}
				return implode(' ', $arrTargetSize);
		}
	}


	/**
	 * Get the variables.
	 */
	public function getVariables($varTheme, $strPath = false)
	{
		$objTheme = $this->findTheme($varTheme);

		if (!isset($this->arrVariables[$objTheme->id])) {
			$this->arrVariables[$objTheme->id] = array();

			$objVariable = $this->Database
				->prepare("SELECT * FROM tl_theme_plus_variable WHERE pid=?")
				->execute($objTheme->id);

			while ($objVariable->next())
			{
				$this->arrVariables[$objTheme->id][$objVariable->name] = $this->renderVariable($objVariable, $strPath);
			}
		}

		return $this->arrVariables[$objTheme->id];
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariables($strCode, $arrVariables = false, $strPath = false)
	{
		if (!$arrVariables) {
			$arrVariables = $this->getVariables(false, $strPath);
		}
		$objVariableReplace = new VariableReplacer($arrVariables);
		return preg_replace_callback('#\$([[:alnum:]_\-]+)#', array(&$objVariableReplace, 'replace'), $strCode);
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariablesByTheme($strCode, $varTheme, $strPath = false)
	{
		$objVariableReplace = new VariableReplacer($this->getVariables($varTheme, $strPath));
		return preg_replace_callback('#\$([[:alnum:]_\-]+)#', array(&$objVariableReplace, 'replace'), $strCode);
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariablesByLayout($strCode, $varLayout, $strPath = false)
	{
		$objVariableReplace = new VariableReplacer($this->getVariables($this->findThemeByLayout($varLayout), $strPath));
		return preg_replace_callback('#\$([[:alnum:]_\-]+)#', array(&$objVariableReplace, 'replace'), $strCode);
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHash($arrVariables)
	{
		$strVariables = '';
		foreach ($arrVariables as $k=> $v)
		{
			$strVariables .= $k . ':' . $v . "\n";
		}
		return md5($strVariables);
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHashByTheme($varTheme)
	{
		return $this->getVariablesHash($this->getVariables($varTheme));
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHashByLayout($varLayout)
	{
		return $this->getVariablesHash($this->getVariables($this->findThemeByLayout($varLayout)));
	}


	/**
	 * Get theme from layout.
	 */
	public function findTheme($varTheme)
	{
		if ($varTheme instanceof Database_Result) {
			return $varTheme;
		}

		$objTheme = $this->Database
			->prepare("SELECT * FROM tl_theme WHERE id=?")
			->execute(is_numeric($varTheme) ? intval($varTheme) : (is_array($varTheme) ? $varTheme['pid'] : $varTheme->pid));
		if ($objTheme->next()) {
			return $objTheme;
		}
		return false;
	}


	/**
	 * Get theme from layout.
	 */
	public function findThemeByLayout($varLayout)
	{
		if (is_numeric($varLayout)) {
			$strSql = "SELECT t.* FROM tl_theme t INNER JOIN tl_layout l ON p.id=l.pid WHERE l.id=?";
		}
		else
		{
			$strSql = "SELECT * FROM tl_theme WHERE id=?";
		}
		$objTheme = $this->Database
			->prepare($strSql)
			->execute(is_numeric($varLayout) ? intval($varLayout) : (is_array($varLayout) ? $varLayout['pid'] : $varLayout->pid));
		if ($objTheme->next()) {
			return $objTheme;
		}
		return false;
	}


	/**
	 * Get theme from page.
	 */
	public function findThemeByPage($varPage)
	{
		$objPage = $this->getPageDetails(is_numeric($varPage) ? intval($varPage) : (is_array($varPage) ? $varPage['id'] : $varPage->id));
		return $this->findThemeByLayout($objPage->layout);
	}


	/**
	 * Detect page aggregate.
	 */
	public function getPageLayoutAggregateState($objPage = false, $objLayout = false)
	{
		// if be user is logged in, disable aggregation
		if ($this->getBELoginStatus()) {
			return false;
		}

		// get state direct from layout object
		if ($objLayout) {
			return $objLayout->aggregate ? true : false;
		}

		// find layout by page and get aggregate state
		if (!$objPage) {
			$objPage = $GLOBALS['objPage'];
		}
		if ($objPage) {
			$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE id=?")
				->execute($GLOBALS['objPage']->layout);
			if ($objLayout->next()) {
				return $objLayout->aggregate ? true : false;
			}
		}

		// the default behaviour is to not aggregate
		return false;
	}


	/**
	 * Aggregate files.
	 */
	public function aggregateFiles($arrFiles)
	{
		$arrAggregatedFiles = array();
		$objAggregator      = null;

		// walk over all files
		foreach ($arrFiles as $objFile)
		{
			$strScope = method_exists($objFile, 'getAggregation') ? ($objFile->getAggregation() == 'theme' ? $objFile->getAggregationScope() : $objFile->getAggregation()) : '';

			// if file is a local css file and it can be aggregated, combine them
			if ($objFile instanceof LocalCssFile && $objFile->isAggregateable()) {
				// if there is no aggregator or a wrong aggregator then create one
				if ($objAggregator == null
					|| !($objAggregator instanceof CssFileAggregator)
					|| $objAggregator->getScope() != $strScope
				) {
					$objAggregator        = new CssFileAggregator($strScope);
					$arrAggregatedFiles[] = $objAggregator;
				}

				// add file to aggregator
				$objAggregator->add($objFile);
			}

			// if file is a local css file and it can be aggregated, combine them
			else if ($objFile instanceof LocalJavaScriptFile && $objFile->isAggregateable()) {
				// if there is no aggregator, create one
				if ($objAggregator == null
					|| !($objAggregator instanceof JavaScriptFileAggregator)
					|| $objAggregator->getScope() != $strScope
				) {
					$objAggregator        = new JavaScriptFileAggregator($strScope);
					$arrAggregatedFiles[] = $objAggregator;
				}

				// add file to aggregator
				$objAggregator->add($objFile);
			}
			// the file can not be aggregated
			else
			{
				// if there is an aggregator, empty the variable
				if ($objAggregator != null) {
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
	public function getCssFiles($arrSortedIds, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		// return if there are no ids or file paths
		$arrFiles = array_filter($arrSortedIds, array($this, 'filter_string'));
		$arrIds   = array_filter($arrSortedIds, array($this, 'filter_int'));
		if (empty($arrIds) && empty($arrFiles)) {
			return array();
		}

		if ($blnAggregate == null && $GLOBALS['objPage']) {
			$blnAggregate = $this->getPageLayoutAggregateState();
		}

		$arrStylesheets = array();

		// collect css and js files into $arrSourcesMap, depending of the conditional comment
		if (count($arrIds)) {
			$objFile = $this->Database->execute("
					SELECT
						*
					FROM
						tl_theme_plus_file
					WHERE
						id IN (" . implode(',', $arrIds) . ")
					AND
						(type = 'css_url' OR type = 'css_file' OR type = 'css_code')");
			while ($objFile->next())
			{
				if ($this->filter($objFile)) {
					continue;
				}

				$strType  = $objFile->type;
				$strValue = $objFile->$strType;
				switch ($strType)
				{
					case 'css_url':
						if (preg_match('#\.less$#i', $strValue)) {
							$arrStylesheets[$objFile->id] = new ExternalLessCssFile($strValue);
						}
						else
						{
							$arrStylesheets[$objFile->id] = new ExternalCssFile($strValue);
						}
						break;

					case 'css_file':
						$objTheme = $this->findTheme($objFile->pid);

						$arrStylesheets[$objFile->id] = LocalThemePlusFile::create($strValue);
						$arrStylesheets[$objFile->id]->setTheme($objTheme);
						$arrStylesheets[$objFile->id]->setAbsolutizePage($blnAbsolutizeUrls ? $objAbsolutizePage : false);
						$arrStylesheets[$objFile->id]->setAggregation($objFile->aggregation);
						break;

					case 'css_code':
						$objTheme = $this->findTheme($objFile->pid);

						$arrStylesheets[$objFile->id] = new CssCode($strValue, $objFile->code_snippet_title);
						$arrStylesheets[$objFile->id]->setTheme($objTheme);
						$arrStylesheets[$objFile->id]->setAbsolutizePage($blnAbsolutizeUrls ? $objAbsolutizePage : false);
						$arrStylesheets[$objFile->id]->setAggregation($objFile->aggregation);
						break;

					default:
						continue;
				}

				$arrStylesheets[$objFile->id]->setMedia($objFile->media);
				$arrStylesheets[$objFile->id]->setCc($objFile->cc);

			}
		}

		// add files by path
		if (count($arrFiles)) {
			foreach ($arrFiles as $strFile)
			{
				$objFile = LocalThemePlusFile::create($strFile);
				if ($objFile && $objFile instanceof LocalCssFile) {
					$arrStylesheets[$strFile] = $objFile;
				}
			}
		}

		// sort array
		$objSortingHelper = new SortingHelper($arrSortedIds);
		uksort($arrStylesheets, array($objSortingHelper, 'cmp'));

		// aggregate
		if ($blnAggregate) {
			$arrStylesheets = $this->aggregateFiles($arrStylesheets);
		}

		return $arrStylesheets;
	}


	/**
	 * Get javascript files by id.
	 */
	public function getJavaScriptFiles($arrSortedIds, $strPosition = false, $blnAggregate = null)
	{
		// return if there are no ids or file paths
		$arrFiles = array_filter($arrSortedIds, array(&$this, 'filter_string'));
		$arrIds   = array_filter($arrSortedIds, array(&$this, 'filter_int'));
		if (empty($arrIds) && empty($arrFiles)) {
			return array();
		}

		if ($blnAggregate == null && $GLOBALS['objPage']) {
			$blnAggregate = $this->getPageLayoutAggregateState();
		}

		$arrJavaScripts = array();

		// collect css and js files into $arrSourcesMap, depending of the conditional comment
		if (count($arrIds)) {
			$objFile = $this->Database
				->prepare("SELECT
						*
					FROM
						tl_theme_plus_file
					WHERE
						id IN (" . implode(',', $arrIds) . ")
					" . ($strPosition ? "
					AND
						position = ?
					" : "") . "
					AND
						(type = 'js_url' OR type = 'js_file' OR type = 'js_code')");
			if ($strPosition) {
				$objFile = $objFile->execute($strPosition);
			}
			else
			{
				$objFile = $objFile->execute();
			}

			while ($objFile->next())
			{
				if ($this->filter($objFile)) {
					continue;
				}

				$strType  = $objFile->type;
				$strValue = $objFile->$strType;
				switch ($strType)
				{
					case 'js_url':
						$arrJavaScripts[$objFile->id] = new ExternalJavaScriptFile($strValue);
						break;

					case 'js_file':
						$objTheme = $this->findTheme($objFile->pid);

						$arrJavaScripts[$objFile->id] = new LocalJavaScriptFile($strValue);
						$arrJavaScripts[$objFile->id]->setTheme($objTheme);
						$arrJavaScripts[$objFile->id]->setAggregation($objFile->aggregation);
						break;

					case 'js_code':
						$objTheme = $this->findTheme($objFile->pid);

						$arrJavaScripts[$objFile->id] = new JavaScriptCode($strValue, $objFile->code_snippet_title);
						$arrJavaScripts[$objFile->id]->setTheme($objTheme);
						$arrJavaScripts[$objFile->id]->setAggregation($objFile->aggregation);
						break;

					default:
						continue;
				}

				$arrJavaScripts[$objFile->id]->setCc($objFile->cc);
			}
		}

		// add files by path
		if (count($arrFiles)) {
			foreach ($arrFiles as $strFile)
			{
				$objFile = LocalThemePlusFile::create($strFile);
				if ($objFile && $objFile instanceof LocalJavaScriptFile) {
					$arrJavaScripts[$strFile] = $objFile;
				}
			}
		}

		// sort array
		$objSortingHelper = new SortingHelper($arrSortedIds);
		uksort($arrJavaScripts, array($objSortingHelper, 'cmp'));

		// aggregate
		if ($blnAggregate) {
			$arrJavaScripts = $this->aggregateFiles($arrJavaScripts);
		}

		return $arrJavaScripts;
	}


	/**
	 * Check the file filter.
	 *
	 * @return Return true if the file filter NOT match, otherwise false.
	 */
	public function filter($objFile)
	{
		if ($objFile->filter) {
			$ua      = $this->Environment->agent;
			$arrRule = deserialize($objFile->filterRule, true);
			foreach ($arrRule as $strRule)
			{
				if (preg_match('#^os-(.*)$#', $strRule, $m)) {
					if ($ua->os == $m[1]) {
						return $objFile->filterInvert ? true : false;
					}
				}
				else if (preg_match('#^browser-(.*?)(?:-(\d+))?$#', $strRule, $m)) {
					if ($ua->browser == $m[1] && (empty($m[2]) || $ua->version == floatval($m[2]))) {
						return $objFile->filterInvert ? true : false;
					}
				}
				else if ($strRule == '@mobile') {
					if ($ua->mobile) {
						return $objFile->filterInvert ? true : false;
					}
				}
			}

			return $objFile->filterInvert ? false : true;
		}
		return false;
	}


	/**
	 * Wrap a javascript src for lazy include.
	 *
	 * @return string
	 */
	public function wrapJavaScriptLazyInclude($strSrc)
	{
		return 'loadAsync(' . json_encode($strSrc) . ');';
	}


	/**
	 * Wrap a javascript src for lazy embedding.
	 *
	 * @return string
	 */
	public function wrapJavaScriptLazyEmbedded($strSource)
	{
		$strBuffer = 'var f=(function(){';
		$strBuffer .= $strSource;
		$strBuffer .= '});';
		$strBuffer .= 'if (window.attachEvent){';
		$strBuffer .= 'window.attachEvent("onload",f);';
		$strBuffer .= '}else{';
		$strBuffer .= 'window.addEventListener("load",f,false);';
		$strBuffer .= '}';
		return $strBuffer;
	}


	/**
	 * Inherit files from pages.
	 *
	 * @param Database_Result $objPage
	 */
	public function inheritFiles(Database_Result $objPage, $strType, $strPosition = false)
	{
		$strField = 'theme_plus_' . $strType;
		if ($objPage->theme_plus_include_files) {
			$arrTemp = deserialize($objPage->$strField, true);

			// filter entries by position
			if ($strPosition && count($arrTemp)) {
				$objFile = $this->Database
					->prepare("SELECT * FROM tl_theme_plus_file WHERE id IN " . implode(',', $arrTemp) . " AND position=? ORDER BY id=" . implode(',id=', $arrTemp))
					->execute($strPosition);
				$arrTemp = $objFile->fetchEach('id');
			}
		}
		else
		{
			$arrTemp = array();
		}

		if ($objPage->pid > 0) {
			$objParentPage = $this->Database->prepare("
					SELECT
						*
					FROM
						tl_page
					WHERE
						id=?")
				->execute($objPage->pid);
			if ($objParentPage->next()) {
				$arrTemp = array_merge
				(
					$arrTemp,
					$this->inheritFiles($objParentPage, $strType)
				);
			}
		}
		return $arrTemp;
	}


	/**
	 * Generate the html code.
	 *
	 * @param array $arrFileIds
	 * @param bool $blnAbsolutizeUrls
	 * @param object $objAbsolutizePage
	 *
	 * @return string
	 */
	public function includeFiles($arrFileIds, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$arrResult = array();

		// add css files
		$arrFiles = $this->getCssFiles($arrFileIds, $blnAggregate, $blnAbsolutizeUrls, $objAbsolutizePage);
		foreach ($arrFiles as $objFile)
		{
			$arrResult[] = $objFile->getIncludeHtml();
		}

		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $objFile)
		{
			$arrResult[] = $objFile->getIncludeHtml();
		}
		return $arrResult;
	}


	/**
	 * Generate the html code.
	 *
	 * @param array $arrFileIds
	 *
	 * @return array
	 */
	public function embedFiles($arrFileIds, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$arrResult = array();

		// add css files
		$arrFiles = $this->getCssFiles($arrFileIds, $blnAbsolutizeUrls, $objAbsolutizePage);
		foreach ($arrFiles as $objFile)
		{
			$arrResult[] = $objFile->getEmbededHtml();
		}

		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $objFile)
		{
			$arrResult[] = $objFile->getEmbededHtml();
		}
		return $arrResult;
	}


	/**
	 * Hook
	 *
	 * @param string $strTag
	 *
	 * @return mixed
	 */
	public function hookReplaceInsertTags($strTag)
	{
		$arrParts = explode('::', $strTag);
		$arrIds   = explode(',', $arrParts[1]);
		switch ($arrParts[0])
		{
			case 'include_theme_file':
				return implode("\n", $this->includeFiles($arrIds)) . "\n";

			case 'embed_theme_file':
				return implode("\n", $this->embedFiles($arrIds)) . "\n";

			// @deprecated
			case 'insert_additional_sources':
				return implode("\n", $this->includeFiles($arrIds)) . "\n";

			// @deprecated
			case 'include_additional_sources':
				return implode("\n", $this->embedFiles($arrIds)) . "\n";
		}

		return false;
	}


	/**
	 * Hook
	 */
	public function hookOutputBackendTemplate($strContent, $strTemplate)
	{
		/*
		if ($strTemplate == 'be_main' && $this->Input->get('table') == 'tl_theme_plus_file')
		{
			$strContent = str_replace('</head>', '<link rel="stylesheet" href="system/modules/theme_plus/html/be.css"></head>', $strContent);
			$strContent = preg_replace('@<a href="contao/main\.php\?do=themes&amp;table=tl_theme_plus_file&amp;id=\d+&amp;act=create&amp;.*".*">.*</a> &#160; :: &#160; @U', '', $strContent);
		}
		*/

		return $strContent;
	}


	/**
	 * Helper function that filter out all non integer values.
	 */
	public function filter_int($string)
	{
		if (is_numeric($string)) {
			return true;
		}
		return false;
	}


	/**
	 * Helper function that filter out all integer values.
	 */
	public function filter_string($string)
	{
		if (is_numeric($string)) {
			return false;
		}
		return true;
	}


	/**
	 * Add mootools from googleapis
	 */
	public function addMooGoogleAPIs($objPage, $objLayout, $objPageRegular)
	{
		$protocol = $this->Environment->ssl ? 'https://' : 'http://';

		$GLOBALS['TL_JAVASCRIPT_FRAMEWORK']['mootools-core'] = $protocol . 'ajax.googleapis.com/ajax/libs/mootools/' . MOOTOOLS . '/mootools-yui-compressed.js';
		$GLOBALS['TL_JAVASCRIPT_FRAMEWORK']['mootools-more'] = 'plugins/mootools/' . MOOTOOLS . '/mootools-more.js';
	}


	/**
	 * Add local mootools
	 */
	public function addMooLocal($objPage, $objLayout, $objPageRegular)
	{
		$GLOBALS['TL_JAVASCRIPT_FRAMEWORK']['mootools-core'] = 'plugins/mootools/' . MOOTOOLS . '/mootools-core.js';
		$GLOBALS['TL_JAVASCRIPT_FRAMEWORK']['mootools-more'] = 'plugins/mootools/' . MOOTOOLS . '/mootools-more.js';
	}
}


/**
 * Sorting helper.
 */
class SortingHelper
{
	/**
	 * Sorted array of ids and paths.
	 */
	protected $arrSortedIds;


	/**
	 * Constructor
	 */
	public function __construct($arrSortedIds)
	{
		$this->arrSortedIds = array_values($arrSortedIds);
	}


	/**
	 * uksort callback
	 */
	public function cmp($a, $b)
	{
		$a = array_search($a, $this->arrSortedIds);
		$b = array_search($b, $this->arrSortedIds);

		// both are equals or not found
		if ($a === $b) {
			return 0;
		}

		// $a not found
		if ($a === false) {
			return -1;
		}

		// $b not found
		if ($b === false) {
			return 1;
		}

		return $a - $b;
	}
}


/**
 * A little helper class that work as callback for preg_replace_callback.
 */
class VariableReplacer extends System
{
	/**
	 * The variables and there values.
	 */
	protected $variables;


	/**
	 * Constructor
	 */
	public function __construct($variables)
	{
		$this->variables = $variables;
	}


	/**
	 * Callback function for preg_replace_callback.
	 * Searching the variable in $this->variables and return the value
	 * or a comment, that the variable does not exists!
	 */
	public function replace($m)
	{
		if (isset($this->variables[$m[1]])) {
			return $this->variables[$m[1]];
		}

		// HOOK: replace undefined variable
		if (isset($GLOBALS['TL_HOOKS']['replaceUndefinedVariable']) && is_array($GLOBALS['TL_HOOKS']['replaceUndefinedVariable'])) {
			foreach ($GLOBALS['TL_HOOKS']['replaceUndefinedVariable'] as $callback)
			{
				$this->import($callback[0]);
				$varResult = $this->$callback[0]->$callback[1]($m[1]);
				if ($varResult) {
					return $varResult;
				}
			}
		}

		return $m[0];
	}
}
