<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ThemePlus
 * 
 * Adding files to the page layout.
 */
class ThemePlusPageRegular extends PageRegular
{
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
	 * @param object
	 * @param object
	 */
	protected function createTemplate(Database_Result $objPage, Database_Result $objLayout)
	{
		// setup the TL_CSS array
		if (!is_array($GLOBALS['TL_CSS']))
		{
			$GLOBALS['TL_CSS'] = array('system/contao.css');
		}
		else
		{
			array_unshift($GLOBALS['TL_CSS'], 'system/contao.css');
		}
		
		// setup the TL_JAVASCRIPT array
		if (!is_array($GLOBALS['TL_JAVASCRIPT']))
		{
			$GLOBALS['TL_JAVASCRIPT'] = array();
		}
		
		parent::createTemplate($objPage, $objLayout);
		
		// Initialize margin
		$arrMargin = array
		(
			'left'   => '0 auto 0 0',
			'center' => '0 auto',
			'right'  => '0 0 0 auto'
		);

		$strFramework = false;

		// HOOK: create framework code
		if (isset($GLOBALS['TL_HOOKS']['generateFrameworkCss']) && is_array($GLOBALS['TL_HOOKS']['generateFrameworkCss']))
		{
			foreach ($GLOBALS['TL_HOOKS']['generateFrameworkCss'] as $callback)
			{
				$this->import($callback[0]);
				$strFramework = $this->$callback[0]->$callback[1]($objPage, $objLayout, $this);
				if ($strFramework !== false)
				{
					break;
				}
			}
		}

		if ($strFramework === false)
		{
			// Wrapper
			if ($objLayout->static)
			{
				$arrSize = deserialize($objLayout->width);
				$strFramework .= sprintf('#wrapper{width:%s;margin:%s;}', $arrSize['value'] . $arrSize['unit'], $arrMargin[$objLayout->align]) . "\n";
			}
	
			// Header
			if ($objLayout->header)
			{
				$arrSize = deserialize($objLayout->headerHeight);
	
				if ($arrSize['value'] != '' && $arrSize['value'] >= 0)
				{
					$strFramework .= sprintf('#header{height:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
				}
			}
	
			$strMain = '';
	
			// Left column
			if ($objLayout->cols == '2cll' || $objLayout->cols == '3cl')
			{
				$arrSize = deserialize($objLayout->widthLeft);
	
				if ($arrSize['value'] != '' && $arrSize['value'] >= 0)
				{
					$strFramework .= sprintf('#left{width:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
					$strMain .= sprintf('margin-left:%s;', $arrSize['value'] . $arrSize['unit']);
				}
			}
	
			// Right column
			if ($objLayout->cols == '2clr' || $objLayout->cols == '3cl')
			{
				$arrSize = deserialize($objLayout->widthRight);
	
				if ($arrSize['value'] != '' && $arrSize['value'] >= 0)
				{
					$strFramework .= sprintf('#right{width:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
					$strMain .= sprintf('margin-right:%s;', $arrSize['value'] . $arrSize['unit']);
				}
			}
	
			// Main column
			if (strlen($strMain))
			{
				$strFramework .= sprintf('#main{%s}', $strMain) . "\n";
			}
	
			// Footer
			if ($objLayout->footer)
			{
				$arrSize = deserialize($objLayout->footerHeight);
	
				if ($arrSize['value'] != '' && $arrSize['value'] >= 0)
				{
					$strFramework .= sprintf('#footer{height:%s;}', $arrSize['value'] . $arrSize['unit']) . "\n";
				}
			}
		}

		$this->Template->framework = '';

		$strKey = substr(md5($strFramework), 0, 8);
		$strFile = 'system/scripts/framework-' . $strKey . '.css';
		
		if (!file_exists(TL_ROOT . '/' . $strFile))
		{
			$objFile = new File($strFile);
			$objFile->write($strFramework);
			$objFile->close();
		}
		
		// Add the framework css file to css list
		array_unshift($GLOBALS['TL_CSS'], $strFile);
		
		// MooTools scripts
		if ($objLayout->mooSource == 'moo_googleapis')
		{
			$protocol = $this->Environment->ssl ? 'https://' : 'http://';

			$this->Template->mooScripts  = '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . $protocol . 'ajax.googleapis.com/ajax/libs/mootools/' . MOOTOOLS . '/mootools-yui-compressed.js"></script>' . "\n";
			$this->Template->mooScripts .= '<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . TL_PLUGINS_URL . 'plugins/mootools/' . MOOTOOLS . '/mootools-more.js"></script>' . "\n";
		}
		else
		{
			$this->Template->mooScripts = '';
			
			array_unshift($GLOBALS['TL_JAVASCRIPT'], 'plugins/mootools/' . MOOTOOLS . '/mootools-core.js');
			array_unshift($GLOBALS['TL_JAVASCRIPT'], 'plugins/mootools/' . MOOTOOLS . '/mootools-more.js');
		}
	}


	/**
	 * Create all header scripts
	 * @param object
	 * @param object
	 */
	protected function createHeaderScripts(Database_Result $objPage, Database_Result $objLayout)
	{
		// stylesheets ---------------------------------------------------------
		$strStyleSheets = '';
		$arrStyleSheets = deserialize($objLayout->stylesheet);
		$strTagEnding = ($objPage->outputFormat == 'xhtml') ? ' />' : '>';

		// get all file ids
		// + from layout
		// + from this page
		// + from parent pages
		$arrFileIds = array_merge
		(
			deserialize($objLayout->theme_plus_files, true),
			$this->ThemePlus->inheritFiles($objPage),
			($objPage->theme_plus_include_files_noinherit ? deserialize($objPage->theme_plus_files_noinherit, true) : array())
		);
		
		// build stylesheets
		$arrStylesheets = array();
		
		// collect internal stylesheets
		if (is_array($GLOBALS['TL_CSS']) && count($GLOBALS['TL_CSS']))
		{
			foreach (array_unique($GLOBALS['TL_CSS']) as $stylesheet)
			{
				if ($stylesheet instanceof ThemePlusFile)
				{
					$arrStylesheets[] = $stylesheet;
				}
				else
				{
					list($stylesheet, $media, $cc) = explode('|', $stylesheet);
					$arrStylesheets[] = new LocalCssFile($stylesheet, $media, $cc);
				}
			}
		}
		
		// User style sheets
		if (is_array($arrStyleSheets) && strlen($arrStyleSheets[0]))
		{
			$objStylesheets = $this->Database->execute("SELECT *, (SELECT MAX(tstamp) FROM tl_style WHERE tl_style.pid=tl_style_sheet.id) AS tstamp2, (SELECT COUNT(*) FROM tl_style WHERE tl_style.selector='@font-face' AND tl_style.pid=tl_style_sheet.id) AS hasFontFace FROM tl_style_sheet WHERE id IN (" . implode(', ', $arrStyleSheets) . ") ORDER BY FIELD(id, " . implode(', ', $arrStyleSheets) . ")");

			while ($objStylesheets->next())
			{
				$media = implode(',', deserialize($objStylesheets->media));

				// Overwrite the media type with a custom media query
				if ($objStylesheets->mediaQuery != '')
				{
					$media = $objStylesheets->mediaQuery;
				}

				// Aggregate regular style sheets
				$arrStylesheets[] = new LocalCssFile('system/scripts/' . $objStylesheets->name . '.css', $media, $objStylesheets->cc);
			}
		}
		
		// Default TinyMCE style sheet
		if (!$objLayout->skipTinymce && file_exists(TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/tinymce.css'))
		{
			$arrStylesheets[] = new LocalCssFile($GLOBALS['TL_CONFIG']['uploadPath'] . '/tinymce.css');
		}
		
		// Theme+ stylesheets
		$arrStylesheets = array_merge
		(
			$arrStylesheets,
			$this->ThemePlus->getCssFiles($arrFileIds, false)
		);
		
		// aggregate stylesheets
		if (!$this->ThemePlus->getBELoginStatus())
		{
			$arrStylesheets = $this->ThemePlus->aggregateFiles($arrStylesheets);
		}
		
		// generate html and add to template
		foreach ($arrStylesheets as $objStylesheet)
		{
			$strStyleSheets .= $objStylesheet->getIncludeHtml() . "\n";
		}
		
		// feeds ---------------------------------------------------------------
		$newsfeeds = deserialize($objLayout->newsfeeds);
		$calendarfeeds = deserialize($objLayout->calendarfeeds);

		// Add newsfeeds
		if (is_array($newsfeeds) && count($newsfeeds) > 0)
		{
			$objFeeds = $this->Database->execute("SELECT * FROM tl_news_archive WHERE makeFeed=1 AND id IN(" . implode(',', array_map('intval', $newsfeeds)) . ")");

			while($objFeeds->next())
			{
				$base = strlen($objFeeds->feedBase) ? $objFeeds->feedBase : $this->Environment->base;
				$strStyleSheets .= '<link type="application/' . $objFeeds->format . '+xml" rel="alternate" href="' . $base . $objFeeds->alias . '.xml" title="' . $objFeeds->title . '"' . $strTagEnding . "\n";
			}
		}

		// Add calendarfeeds
		if (is_array($calendarfeeds) && count($calendarfeeds) > 0)
		{
			$objFeeds = $this->Database->execute("SELECT * FROM tl_calendar WHERE makeFeed=1 AND id IN(" . implode(',', array_map('intval', $calendarfeeds)) . ")");

			while($objFeeds->next())
			{
				$base = strlen($objFeeds->feedBase) ? $objFeeds->feedBase : $this->Environment->base;
				$strStyleSheets .= '<link type="application/' . $objFeeds->format . '+xml" rel="alternate" href="' . $base . $objFeeds->alias . '.xml" title="' . $objFeeds->title . '"' . $strTagEnding . "\n";
			}
		}

		// javascripts ---------------------------------------------------------
		$strHeadTags = '';
		$arrJavaScripts = array();
		
		// collect internal javascripts
		if (is_array($GLOBALS['TL_JAVASCRIPT']) && count($GLOBALS['TL_JAVASCRIPT']))
		{
			foreach (array_unique($GLOBALS['TL_JAVASCRIPT']) as $javascript)
			{
				$arrJavaScripts[] = new LocalJavaScriptFile($javascript);
			}
		}
		
		// add theme+ javascripts
		$arrJavaScripts = array_merge
		(
			$arrJavaScripts,
			$this->ThemePlus->getJavaScriptFiles($arrFileIds)
		);
		
		// aggregate javascripts
		if (!$this->ThemePlus->getBELoginStatus())
		{
			$arrJavaScripts = $this->ThemePlus->aggregateFiles($arrJavaScripts);
		}
		
		// add them to the layout
		foreach ($arrJavaScripts as $objJavaScript)
		{
			$strHeadTags .= $objJavaScript->getIncludeHtml() . "\n";
		}
		
		// Add internal <head> tags
		if (is_array($GLOBALS['TL_HEAD']) && count($GLOBALS['TL_HEAD']))
		{
			foreach (array_unique($GLOBALS['TL_HEAD']) as $head)
			{
				$strHeadTags .= trim($head) . "\n";
			}
		}

		// Add <head> tags
		if (($strHead = trim($objLayout->head)) != false)
		{
			$strHeadTags .= $strHead . "\n";
		}

		$this->Template->stylesheets = $strStyleSheets;
		$this->Template->head = $strHeadTags;
	}
}

?>