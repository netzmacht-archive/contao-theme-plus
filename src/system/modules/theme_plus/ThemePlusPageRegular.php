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
class ThemePlusPageRegular extends PageRegular
{
	/**
	 * The ThemePlus object
	 *
	 * @var ThemePlus
	 */
	protected $ThemePlus;


	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('ThemePlus');
	}


	/**
	 * Create a new template
	 *
	 * @param object
	 * @param object
	 */
	protected function createTemplate(Database_Result $objPage, Database_Result $objLayout)
	{
		// setup the TL_CSS array
		if (!is_array($GLOBALS['TL_CSS'])) {
			$GLOBALS['TL_CSS'] = array();
		}

		if (version_compare(VERSION, '2.11', '<') ? !$objLayout->theme_plus_exclude_contaocss : !$objLayout->skipFramework) {
			array_unshift($GLOBALS['TL_CSS'], 'system/contao.css');
		}

		// setup the TL_JAVASCRIPT array
		if (!is_array($GLOBALS['TL_JAVASCRIPT'])) {
			$GLOBALS['TL_JAVASCRIPT'] = array();
		}

		parent::createTemplate($objPage, $objLayout);

		if (!$objLayout->theme_plus_exclude_frameworkcss) {
			$strFramework = false;

			// HOOK: create framework code
			if (isset($GLOBALS['TL_HOOKS']['generateFrameworkCss']) && is_array($GLOBALS['TL_HOOKS']['generateFrameworkCss'])) {
				foreach ($GLOBALS['TL_HOOKS']['generateFrameworkCss'] as $callback)
				{
					$this->import($callback[0]);
					$strFramework = $this->$callback[0]->$callback[1]($objPage, $objLayout, $this);
					if ($strFramework !== false) {
						break;
					}
				}
			}

			if ($strFramework === false) {
				$strFramework = $this->generateFramework($objPage, $objLayout);
			}

			$strKey  = substr(md5($strFramework), 0, 8);
			$strFile = 'system/scripts/framework-' . $strKey . '.css';

			if (!file_exists(TL_ROOT . '/' . $strFile)) {
				$objFile = new File($strFile);
				$objFile->write($strFramework);
				$objFile->close();
			}

			// Add the framework css file to css list
			array_unshift($GLOBALS['TL_CSS'], $strFile);
		}

		$this->Template->framework  = '';
		$this->Template->mooScripts = '';

		// JavaScript framework
		$GLOBALS['TL_JAVASCRIPT_FRAMEWORK'] = array();
		foreach ($GLOBALS['TL_SCRIPT_FRAMEWORKS'] as $k=> $v)
		{
			// check if there is an on/off trigger
			if (isset($v['__trigger__'])) {
				// name of the trigger
				$t = $v['__trigger__'];

				// skip if trigger is not active
				if (!$objLayout->$t) {
					continue;
				}
			}

			if (isset($v[$objLayout->$k])) {
				// call the callback
				$callback = $v[$objLayout->$k];
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($objPage, $objLayout, $this);
			}
		}
	}


	/**
	 * Create all header scripts
	 *
	 * @param object
	 * @param object
	 */
	protected function createHeaderScripts(Database_Result $objPage, Database_Result $objLayout)
	{
		if (!is_array($objLayout->theme_plus_exclude_files)) {
			$objLayout->theme_plus_exclude_files = deserialize($objLayout->theme_plus_exclude_files, true);
		}
		if (count($objLayout->theme_plus_exclude_files) > 0) {
			foreach ($objLayout->theme_plus_exclude_files as $v)
			{
				if ($v[0]) {
					$GLOBALS['TL_THEME_EXCLUDE'][] = $v[0];
				}
			}
		}

		$strTagEnding = ($objPage->outputFormat == 'xhtml') ? ' />' : '>';
		$strStyleSheets = '';

		// Google web fonts ----------------------------------------------------
		if ($objLayout->webfonts != '')
		{
			$protocol = $this->Environment->ssl ? 'https://' : 'http://';
			$strStyleSheets .= '<link' . (($objPage->outputFormat == 'xhtml') ? ' type="text/css"' : '') .' rel="stylesheet" href="' . $protocol . 'fonts.googleapis.com/css?family=' . $objLayout->webfonts . '"' . $strTagEnding . "\n";
		}

		// stylesheets ---------------------------------------------------------
		$arrStyleSheets = deserialize($objLayout->stylesheet);
		$strTagEnding   = ($objPage->outputFormat == 'xhtml') ? ' />' : '>';

		// build stylesheets
		$arrStylesheets = array();

		// collect internal stylesheets
		if (is_array($GLOBALS['TL_CSS']) && count($GLOBALS['TL_CSS'])) {
			foreach (array_unique($GLOBALS['TL_CSS']) as $stylesheet)
			{
				$objFile = false;

				// split path/url, media and cc
				if (is_string($stylesheet)) {
					list($stylesheet, $media, $cc, $static) = explode('|', $stylesheet);

					// strip the static urls
					$stylesheet = $this->stripStaticURL($stylesheet);
					$theme      = $this->ThemePlus->findThemeByLayout($objLayout);
				}
				else
				{
					$media  = false;
					$cc     = false;
					$theme  = false;
					$static = '';
				}

				// if $cc contains 'static', switch $cc and $static contents
				if ($cc == 'static') {
					$tmp    = $static;
					$static = $cc;
					$cc     = $tmp;
				}

				// $static is not supported in this version!!!

				// add unmodified if its a ThemePlusFile object
				if ($stylesheet instanceof ThemePlusFile) {
					$objFile = $stylesheet;
				}

				// use as local path
				else if (!preg_match('#^\w+://#', $stylesheet)) {
					$objFile = LocalThemePlusFile::create($stylesheet);
				}

				// use as external url
				else
				{
					$objFile = ExternalThemePlusFile::create($stylesheet);
				}

				// fallback, use as external url, without checks
				if ($objFile && $objFile instanceof CssFile) {
					if ($media) {
						$objFile->setMedia($media);
					}
					if ($cc) {
						$objFile->setCc($cc);
					}
					if ($theme) {
						$objFile->setTheme($theme);
					}

					$arrStylesheets[] = $objFile;
				}
			}
		}

		// User style sheets
		if (is_array($arrStyleSheets) && strlen($arrStyleSheets[0])) {
			$objStylesheets = $this->Database->execute("SELECT *, (SELECT MAX(tstamp) FROM tl_style WHERE tl_style.pid=tl_style_sheet.id) AS tstamp2, (SELECT COUNT(*) FROM tl_style WHERE tl_style.selector='@font-face' AND tl_style.pid=tl_style_sheet.id) AS hasFontFace FROM tl_style_sheet WHERE id IN (" . implode(', ', $arrStyleSheets) . ") ORDER BY FIELD(id, " . implode(', ', $arrStyleSheets) . ")");

			while ($objStylesheets->next())
			{
				$media = implode(',', deserialize($objStylesheets->media));

				// Overwrite the media type with a custom media query
				if ($objStylesheets->mediaQuery != '') {
					$media = $objStylesheets->mediaQuery;
				}

				// Aggregate regular style sheets
				$objFile = new LocalCssFile('system/scripts/' . $objStylesheets->name . '.css');
				if ($media) {
					$objFile->setMedia($media);
				}
				if ($objStylesheets->cc) {
					$objFile->setCc($objStylesheets->cc);
				}
				$arrStylesheets[] = $objFile;
			}
		}

		// Default TinyMCE style sheet
		if (!$objLayout->skipTinymce && file_exists(TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/tinymce.css')) {
			$arrStylesheets[] = LocalThemePlusFile::create($GLOBALS['TL_CONFIG']['uploadPath'] . '/tinymce.css');
		}

		// get all other stylesheets

		// + from layout
		$arrLayoutStylesheetIds = deserialize($objLayout->theme_plus_stylesheets, true);
		if (count($arrLayoutStylesheetIds)) {
			$objFile = $this->Database
				->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrLayoutStylesheetIds) . ') ORDER BY sorting');
			$arrTemp = $this->ThemePlus->getCssFiles($objFile->fetchEach('id'), false);
			foreach ($arrTemp as $k=> $v)
			{
				if ($v instanceof LocalThemePlusFile) {
					$objTheme = $v->getTheme();
					$v->setAggregationScope('theme:' . ($objTheme ? $objTheme->id : $objLayout->pid));
				}
			}
			$arrStylesheets = array_merge
			(
				$arrStylesheets,
				array_values($arrTemp)
			);
		}

		// + from this page
		$arrPagesStylesheetIds = $this->ThemePlus->inheritFiles($objPage, 'stylesheets');
		$arrTemp               = $this->ThemePlus->getCssFiles($arrPagesStylesheetIds, false);
		foreach ($arrTemp as $k=> $v)
		{
			$v->setAggregationScope('pages');
		}
		$arrStylesheets = array_merge
		(
			$arrStylesheets,
			array_values($arrTemp)
		);

		// + from parent pages
		$arrPageStylesheetIds = ($objPage->theme_plus_include_files_noinherit ? deserialize($objPage->theme_plus_stylesheets_noinherit, true) : array());
		$arrTemp              = $this->ThemePlus->getCssFiles($arrPageStylesheetIds, false);
		foreach ($arrTemp as $k=> $v)
		{
			$v->setAggregationScope('page');
		}
		$arrStylesheets = array_merge
		(
			$arrStylesheets,
			array_values($arrTemp)
		);

		// filter stylesheets
		$arrTemp        = $arrStylesheets;
		$arrStylesheets = array();
		foreach ($arrTemp as $objStylesheet)
		{
			if (is_array($GLOBALS['TL_THEME_EXCLUDE']) &&
				in_array($objStylesheet instanceof LocalThemePlusFile ? $objStylesheet->getOriginFile() : $objStylesheet->getUrl(), $GLOBALS['TL_THEME_EXCLUDE'])
			) {
				// skip excluded files
				continue;
			}
			$arrStylesheets[] = $objStylesheet;
		}

		// aggregate stylesheets
		if (!$this->ThemePlus->getBELoginStatus()) {
			$arrStylesheets = $this->ThemePlus->aggregateFiles($arrStylesheets);
		}

		// generate html and add to template
		foreach ($arrStylesheets as $objStylesheet)
		{
			$strStyleSheets .= $objStylesheet->getIncludeHtml() . "\n";
		}

		// feeds ---------------------------------------------------------------
		$newsfeeds     = deserialize($objLayout->newsfeeds);
		$calendarfeeds = deserialize($objLayout->calendarfeeds);

		// Add newsfeeds
		if (is_array($newsfeeds) && count($newsfeeds) > 0) {
			$objFeeds = $this->Database->execute("SELECT * FROM tl_news_archive WHERE makeFeed=1 AND id IN(" . implode(',', array_map('intval', $newsfeeds)) . ")");

			while ($objFeeds->next())
			{
				$base = strlen($objFeeds->feedBase) ? $objFeeds->feedBase : $this->Environment->base;
				$strStyleSheets .= '<link type="application/' . $objFeeds->format . '+xml" rel="alternate" href="' . $base . $objFeeds->alias . '.xml" title="' . $objFeeds->title . '"' . $strTagEnding . "\n";
			}
		}

		// Add calendarfeeds
		if (is_array($calendarfeeds) && count($calendarfeeds) > 0) {
			$objFeeds = $this->Database->execute("SELECT * FROM tl_calendar WHERE makeFeed=1 AND id IN(" . implode(',', array_map('intval', $calendarfeeds)) . ")");

			while ($objFeeds->next())
			{
				$base = strlen($objFeeds->feedBase) ? $objFeeds->feedBase : $this->Environment->base;
				$strStyleSheets .= '<link type="application/' . $objFeeds->format . '+xml" rel="alternate" href="' . $base . $objFeeds->alias . '.xml" title="' . $objFeeds->title . '"' . $strTagEnding . "\n";
			}
		}

		// javascripts ---------------------------------------------------------
		$strHeadTags = '';

		if ($objLayout->theme_plus_javascript_lazy_load
			&& $objLayout->theme_plus_default_javascript_position != 'body'
		) {
			$objFile = new LocalJavaScriptFile('system/modules/theme_plus/html/async.js');
			$strHeadTags .= $objFile->getEmbeddedHtml(false);
		}

		$strHeadTags .= $this->collectJavaScripts('head', $objPage, $objLayout);

		// Add internal <head> tags
		if (is_array($GLOBALS['TL_HEAD']) && count($GLOBALS['TL_HEAD'])) {
			foreach (array_unique($GLOBALS['TL_HEAD']) as $head)
			{
				$strHeadTags .= trim($head) . "\n";
			}
		}

		// Add <head> tags
		if (($strHead = trim($objLayout->head)) != false) {
			$strHeadTags .= $strHead . "\n";
		}

		$this->Template->stylesheets = $strStyleSheets;
		$this->Template->head        = $strHeadTags;
	}


	/**
	 * Create all footer scripts
	 *
	 * @param object
	 * @param object
	 */
	protected function createFooterScripts(Database_Result $objPage, Database_Result $objLayout)
	{
		parent::createFooterScripts($objPage, $objLayout);

		$arrDynamicFile = array();
		if (is_array($GLOBALS['TL_JAVASCRIPT_BODY']) && count($GLOBALS['TL_JAVASCRIPT_BODY'])) {
			$arrDynamicFile = $this->storeJavaScriptCode($GLOBALS['TL_JAVASCRIPT_BODY']);
		}

		$strCode = '';

		if (is_array($GLOBALS['TL_JAVASCRIPT_CODE_BODY']) && count($GLOBALS['TL_JAVASCRIPT_CODE_BODY'])) {
			$arrFiles = $this->storeJavaScriptCode($GLOBALS['TL_JAVASCRIPT_CODE_BODY']);
			if ($objLayout->theme_plus_javascript_lazy_load) {
				$arrDynamicFile = array_merge(
					$arrDynamicFile,
					array_values($arrFiles)
				);
			}
			else
			{
				$arrFiles = $this->ThemePlus->aggregateFiles($arrFiles);
				foreach ($arrFiles as $objFile)
				{
					$strCode .= $objFile->getEmbeddedHtml(false) . "\n";
				}
			}
		}

		$strCode = $this->collectJavaScripts('body', $objPage, $objLayout, $arrDynamicFile) . $strCode;

		if ($objLayout->theme_plus_javascript_lazy_load
			&& $objLayout->theme_plus_default_javascript_position == 'body'
		) {
			$objFile = new LocalJavaScriptFile('system/modules/theme_plus/html/async.js');
			$strCode = $objFile->getEmbeddedHtml(false) . $strCode;
		}

		$this->Template->mootools = $strCode . $this->Template->mootools;
	}


	/**
	 * Generate framework css
	 */
	protected function generateFramework(Database_Result $objPage, Database_Result $objLayout)
	{
		$strFramework = '';

		// Initialize margin
		$arrMargin = array
		(
			'left'   => '0 auto 0 0',
			'center' => '0 auto',
			'right'  => '0 0 0 auto'
		);

		// Wrapper
		if ($objLayout->static) {
			$arrSize = deserialize($objLayout->width);
			$strFramework .= sprintf('#wrapper{width:%s;margin:%s;}', $arrSize['value'] . $arrSize['unit'], $arrMargin[$objLayout->align]) . "\n";
		}

		// Header
		if ($objLayout->header) {
			$arrSize = deserialize($objLayout->headerHeight);

			if ($arrSize['value'] != '' && $arrSize['value'] >= 0) {
				$strFramework .= sprintf('#header{height:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
			}
		}

		$strMain = '';

		// Left column
		if ($objLayout->cols == '2cll' || $objLayout->cols == '3cl') {
			$arrSize = deserialize($objLayout->widthLeft);

			if ($arrSize['value'] != '' && $arrSize['value'] >= 0) {
				$strFramework .= sprintf('#left{width:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
				$strMain .= sprintf('margin-left:%s;', $arrSize['value'] . $arrSize['unit']);
			}
		}

		// Right column
		if ($objLayout->cols == '2clr' || $objLayout->cols == '3cl') {
			$arrSize = deserialize($objLayout->widthRight);

			if ($arrSize['value'] != '' && $arrSize['value'] >= 0) {
				$strFramework .= sprintf('#right{width:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
				$strMain .= sprintf('margin-right:%s;', $arrSize['value'] . $arrSize['unit']);
			}
		}

		// Main column
		if (strlen($strMain)) {
			$strFramework .= sprintf('#main{%s}', $strMain) . "\n";
		}

		// Footer
		if ($objLayout->footer) {
			$arrSize = deserialize($objLayout->footerHeight);

			if ($arrSize['value'] != '' && $arrSize['value'] >= 0) {
				$strFramework .= sprintf('#footer{height:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
			}
		}

		return $strFramework;
	}


	/**
	 * @param $arrCode
	 *
	 * @return array
	 */
	protected function storeJavaScriptCode($arrCode)
	{
		$arrFiles = array();
		foreach ($arrCode as $k=> $v)
		{
			if ($v instanceof ThemePlusFile) {
				$arrFiles[] = $v;
			}
			else
			{
				$objFile = new JavaScriptCode($v, $k);
				$objFile->setAggregation('page');
				$objFile->setAggregationScope('page');
				$arrFiles[] = $objFile;
			}
		}

		return $arrFiles;
	}


	/**
	 * Collect all javascript.
	 *
	 * @param string $strPosition
	 * @param Database_Result $objPage
	 * @param Database_Result $objLayout
	 * @param array $arrAdditionalFiles
	 *
	 * @return string
	 */
	protected function collectJavaScripts($strPosition, Database_Result $objPage, Database_Result $objLayout, $arrAdditionalFiles = false)
	{
		$strBuffer      = '';
		$arrJavaScripts = array();

		// collect javascript framework
		if ($strPosition == 'head'
			&& ($objLayout->theme_plus_default_javascript_position == 'head'
				|| $objLayout->theme_plus_default_javascript_position == 'head+body')
			|| $strPosition == 'body'
				&& $objLayout->theme_plus_default_javascript_position == 'body'
		) {
			foreach ($GLOBALS['TL_JAVASCRIPT_FRAMEWORK'] as $javascript)
			{
				$objFile = false;

				// split path/url and cc
				if (is_string($javascript)) {
					// strip the static urls
					$javascript = $this->stripStaticURL($javascript);

					$theme = $this->ThemePlus->findThemeByLayout($objLayout);
				}
				else
				{
					$theme = false;
				}

				// add unmodified if its a ThemePlusFile object
				if ($javascript instanceof ThemePlusFile) {
					$objFile = $javascript;
				}

				// use as external url
				else if (preg_match('#^\w+://#', $javascript)) {
					$objFile = ExternalThemePlusFile::create($javascript);
				}

				// use as local path
				else
				{
					$objFile = LocalThemePlusFile::create($javascript);
				}

				// fallback, use as external url, without checks
				if (!$objFile) {
					$objFile = new ExternalJavaScriptFile($javascript);
				}

				// only add, if its a javascript file object
				if ($objFile && $objFile instanceof JavaScriptFile) {
					if ($theme && method_exists($objFile, 'setTheme')) {
						$objFile->setTheme($theme);
					}

					$arrJavaScripts[] = $objFile;
				}
			}
		}

		// collect internal javascripts
		if (is_array($GLOBALS['TL_JAVASCRIPT']) && count($GLOBALS['TL_JAVASCRIPT'])) {
			foreach (array_unique($GLOBALS['TL_JAVASCRIPT']) as $javascript)
			{
				$objFile = false;

				// split path/url and cc
				if (is_string($javascript)) {
					list($javascript, $cc, $position, $static) = explode('|', $javascript);

					// if $cc contains 'static', switch $cc and $static contents
					if ($cc == 'static') {
						$tmp    = $static;
						$static = $cc;
						$cc     = $tmp;
					}
					// if $position contains 'static', switch $position and $static contents
					else if ($position == 'static') {
						$tmp      = $static;
						$static   = $position;
						$position = $cc;
					}

					// check if no cc is set
					if ($cc == 'head' || $cc == 'body') {
						$position = $cc;
						$cc       = '';
					}

					// strip the static urls
					$javascript = $this->stripStaticURL($javascript);
				}
				else
				{
					$cc       = false;
					$position = false;
					$static   = '';
				}

				// $static is not supported in this version!!!

				if (!$position) {
					$position = $objLayout->theme_plus_default_javascript_position == 'head+body'
						? 'body'
						: $objLayout->theme_plus_default_javascript_position;
				}

				// add unmodified if its a ThemePlusFile object
				if ($javascript instanceof ThemePlusFile) {
					$objFile = $javascript;
				}

				// use as local path
				else if (!preg_match('#^\w+://#', $javascript)) {
					$objFile = LocalThemePlusFile::create($javascript);
				}

				// use as external url
				else
				{
					$objFile = new ExternalJavaScriptFile($javascript);
				}

				if ($objFile && !$objFile->getPosition()) {
					$objFile->setPosition($position);
				}

				// only add, if its a javascript file object and it is located in the position
				if ($objFile
					&& $objFile instanceof JavaScriptFile
					&& $objFile->getPosition() == $strPosition
				) {
					if ($cc) {
						$objFile->setCc($cc);
					}

					$arrJavaScripts[] = $objFile;
				}
			}
		}

		// get other javascripts

		// + from layout
		$arrLayoutJavaScriptIds = deserialize($objLayout->theme_plus_javascripts, true);
		if (count($arrLayoutJavaScriptIds)) {
			$objFile = $this->Database
				->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrLayoutJavaScriptIds) . ') ORDER BY sorting');
			$arrTemp = $this->ThemePlus->getJavaScriptFiles($objFile->fetchEach('id'), $strPosition, false);
			foreach ($arrTemp as $k=> $v)
			{
				if ($v instanceof LocalThemePlusFile) {
					$objTheme = $v->getTheme();
					$v->setAggregationScope('theme:' . ($objTheme ? $objTheme->id : $objLayout->pid));
				}
			}
			$arrJavaScripts = array_merge
			(
				$arrJavaScripts,
				array_values($arrTemp)
			);
		}

		// + from this page
		$arrPagesJavaScriptIds = $this->ThemePlus->inheritFiles($objPage, 'javascripts');
		$arrTemp               = $this->ThemePlus->getJavaScriptFiles($arrPagesJavaScriptIds, $strPosition, false);
		foreach ($arrTemp as $k=> $v)
		{
			if ($v instanceof LocalThemePlusFile) {
				$objTheme = $v->getTheme();
				$v->setAggregationScope('pages');
			}
		}
		$arrJavaScripts = array_merge
		(
			$arrJavaScripts,
			array_values($arrTemp)
		);

		// + from parent pages
		$arrPageJavaScriptIds = ($objPage->theme_plus_include_files_noinherit ? deserialize($objPage->theme_plus_javascripts_noinherit, true) : array());
		$arrTemp              = $this->ThemePlus->getJavaScriptFiles($arrPageJavaScriptIds, $strPosition, false);
		foreach ($arrTemp as $k=> $v)
		{
			if ($v instanceof LocalThemePlusFile) {
				$objTheme = $v->getTheme();
				$v->setAggregationScope('page');
			}
		}
		$arrJavaScripts = array_merge
		(
			$arrJavaScripts,
			array_values($arrTemp)
		);

		if ($arrAdditionalFiles) {
			$arrJavaScripts = array_merge
			(
				$arrJavaScripts,
				array_values($arrAdditionalFiles)
			);
		}

		// filter files
		$arrTemp        = $arrJavaScripts;
		$arrJavaScripts = array();
		foreach ($arrTemp as $objJavaScript)
		{
			if (is_array($GLOBALS['TL_THEME_EXCLUDE']) &&
				in_array($objJavaScript instanceof LocalThemePlusFile ? $objJavaScript->getOriginFile() : $objJavaScript->getUrl(), $GLOBALS['TL_THEME_EXCLUDE'])
			) {
				// skip excluded files
				continue;
			}
			$arrJavaScripts[] = $objJavaScript;
		}

		// aggregate javascripts
		if (!$this->ThemePlus->getBELoginStatus()) {
			$arrJavaScripts = $this->ThemePlus->aggregateFiles($arrJavaScripts);
		}

		// add them to the layout
		foreach ($arrJavaScripts as $objJavaScript)
		{
			$strBuffer .= $objJavaScript->getIncludeHtml($objLayout->theme_plus_javascript_lazy_load ? true : false) . "\n";
		}

		return $strBuffer;
	}


	/**
	 * Strip static urls.
	 */
	protected function stripStaticURL($strUrl)
	{
		if (defined('TL_FILES_URL') && strlen(TL_FILES_URL) > 0 && strpos($strUrl, TL_FILES_URL) === 0) {
			return substr($strUrl, strlen(TL_FILES_URL));
		}
		if (defined('TL_SCRIPT_URL') && strlen(TL_SCRIPT_URL) > 0 && strpos($strUrl, TL_SCRIPT_URL) === 0) {
			return substr($strUrl, strlen(TL_SCRIPT_URL));
		}
		if (defined('TL_PLUGINS_URL') && strlen(TL_PLUGINS_URL) > 0 && strpos($strUrl, TL_PLUGINS_URL) === 0) {
			return substr($strUrl, strlen(TL_PLUGINS_URL));
		}
		return $strUrl;
	}
}
