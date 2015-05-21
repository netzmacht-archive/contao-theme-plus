<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\ThemePlus;

use Template;
use FrontendTemplate;
use Bit3\Contao\ThemePlus\DataContainer\File;
use Bit3\Contao\ThemePlus\Model\StylesheetModel;
use Bit3\Contao\ThemePlus\Model\JavaScriptModel;
use Bit3\Contao\ThemePlus\Model\VariableModel;
use ContaoAssetic\AsseticFactory;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;

/**
 * Class ThemePlus
 *
 * Adding files to the page layout.
 */
class ThemePlus
{
	const BROWSER_IDENT_OVERWRITE = 'THEME_PLUS_BROWSER_IDENT_OVERWRITE';

	/**
	 * Singleton
	 */
	private static $instance = null;


	/**
	 * Get the singleton instance.
	 *
	 * @return ThemePlus
	 */
	public static function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new ThemePlus();

			ThemePlusEnvironment::getInstance();

			if (!isset($_SESSION['THEME_PLUS_ASSETS'])) {
				$_SESSION['THEME_PLUS_ASSETS'] = array();
			}

			// Add new assetic filter factory
			AsseticFactory::registerFilterFactory(new Filter\ThemePlusFilterFactory());
		}
		return static::$instance;
	}

	/**
	 * @return \Ikimea\Browser\Browser
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::getBrowserDetect() instead
	 */
	public static function getBrowserDetect()
	{
		trigger_error(
			'Deprecated, use \ThemePlus\ThemePlusEnvironment::getBrowserDetect() instead!',
			E_USER_DEPRECATED
		);
		return ThemePlusEnvironment::getBrowserDetect();
	}

	/**
	 * @return \Mobile_Detect
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::getMobileDetect() instead
	 */
	public static function getMobileDetect()
	{
		trigger_error('Deprecated, use \ThemePlus\ThemePlusEnvironment::getMobileDetect() instead!', E_USER_DEPRECATED);
		return ThemePlusEnvironment::getMobileDetect();
	}

	/**
	 * The variables cache.
	 */
	protected $arrVariables = null;

	/**
	 * List of all added files.
	 *
	 * @var array
	 */
	protected $files = array();

	/**
	 * @var array|string[]
	 */
	protected $excludeList = array();

	/**
	 * Singleton constructor.
	 */
	protected function __construct()
	{
	}

	/**
	 * Get productive mode status.
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::isLiveMode() instead
	 */
	public static function isLiveMode()
	{
		trigger_error('Deprecated, use \ThemePlus\ThemePlusEnvironment::isLiveMode() instead!', E_USER_DEPRECATED);
		return ThemePlusEnvironment::isLiveMode();
	}


	/**
	 * Set productive mode.
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::setLiveMode() instead
	 */
	public static function setLiveMode($liveMode = true)
	{
		trigger_error('Deprecated, use \ThemePlus\ThemePlusEnvironment::setLiveMode() instead!', E_USER_DEPRECATED);
		ThemePlusEnvironment::setLiveMode($liveMode);
	}


	/**
	 * Get productive mode status.
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::isDesignerMode() instead
	 */
	public static function isDesignerMode()
	{
		trigger_error('Deprecated, use \ThemePlus\ThemePlusEnvironment::isDesignerMode() instead!', E_USER_DEPRECATED);
		return ThemePlusEnvironment::isDesignerMode();
	}


	/**
	 * Set designer mode.
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::setDesignerMode() instead
	 */
	public static function setDesignerMode($designerMode = true)
	{
		trigger_error('Deprecated, use \ThemePlus\ThemePlusEnvironment::setDesignerMode() instead!', E_USER_DEPRECATED);
		ThemePlusEnvironment::setDesignerMode($designerMode);
	}


	/**
	 * Determine if the pre-compile mode is enabled.
	 *
	 * @return bool
	 *
	 * @deprecated use \ThemePlus\ThemePlusEnvironment::isInPreCompileMode() instead
	 */
	public static function isInPreCompileMode()
	{
		trigger_error(
			'Deprecated, use \ThemePlus\ThemePlusEnvironment::isInPreCompileMode() instead!',
			E_USER_DEPRECATED
		);
		return ThemePlusEnvironment::isInPreCompileMode();
	}

	/**
	 * Calculate the target path for the asset.
	 *
	 * @param \Assetic\Asset\AssetInterface $asset
	 * @param                               $suffix
	 *
	 * @return string
	 */
	public static function getAssetPath(AssetInterface $asset, $suffix)
	{
		$filters = array();
		foreach ($asset->getFilters() as $v) {
			$filters[] = get_class($v);
		}
		$filters = '[' . implode(
				',',
				$filters
			) . ']';

		if (!is_dir(TL_ROOT . '/assets/' . $suffix)) {
			mkdir(TL_ROOT . '/assets/' . $suffix);
		}

		// calculate path for collections
		if ($asset instanceof AssetCollection) {
			$string = $filters;
			foreach ($asset->all() as $child) {
				$string .= '-' . static::getAssetPath(
						$child,
						$suffix
					);
			}
			return 'assets/' . $suffix . '/' . substr(
				md5($string),
				0,
				8
			) . '-collection.' . $suffix;
		}

		// calculate cache path from content
		else if ($asset instanceof StringAsset) {
			return 'assets/' . $suffix . '/' . substr(
				md5($filters . '-' . $asset->getContent() . '-' . $asset->getLastModified()),
				0,
				8
			) . '-' . basename($asset->getSourcePath()) . '.' . $suffix;
		}

		// calculate cache path from source path
		else {
			return 'assets/' . $suffix . '/' . substr(
				md5($filters . '-' . $asset->getSourcePath() . '-' . $asset->getLastModified()),
				0,
				8
			) . '-' . basename(
				$asset->getSourcePath(),
				'.' . $suffix
			) . '.' . $suffix;
		}
	}

	/**
	 * Store an asset.
	 *
	 * @param \Assetic\Asset\AssetInterface $asset
	 * @param                               $suffix
	 *
	 * @return string
	 */
	public static function storeAsset(AssetInterface $asset, $suffix, $additionalFilters = null)
	{
		$path = static::getAssetPath(
			$asset,
			$suffix
		);
		$asset->setTargetPath($path);

		if (!file_exists(TL_ROOT . '/' . $path)) {
			$file = new \File($path);
			$file->write(
				$asset->dump(
					$additionalFilters
						? new \Assetic\Filter\FilterCollection($additionalFilters)
						: null
				)
			);
			$file->close();
		}

		return $path;
	}

	/**
	 * Shorthand check if current request is from a desktop.
	 *
	 * @return bool
	 */
	public static function isDesktop()
	{
		$browserIdentOverwrite = json_decode(
			\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
		);

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return $browserIdentOverwrite->platform == 'desktop';
		}

		return !(ThemePlusEnvironment::getMobileDetect()
				->isTablet() || ThemePlusEnvironment::getMobileDetect()
				->isMobile());
	}

	/**
	 * Shorthand check if current request is from a tablet.
	 *
	 * @return bool
	 */
	public static function isTabled()
	{
		trigger_error('Typo in method. Use isTablet instead.', E_USER_DEPRECATED);
		return static::isTablet();
	}

	/**
	 * Shorthand check if current request is from a tablet.
	 *
	 * @return bool
	 */
	public static function isTablet()
	{
		$browserIdentOverwrite = json_decode(
			\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
		);

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return in_array($browserIdentOverwrite->platform, array('tablet', 'mobile'));
		}

		return ThemePlusEnvironment::getMobileDetect()
			->isTablet();
	}

	/**
	 * Shorthand check if current request is from a smartphone.
	 *
	 * @return bool
	 */
	public static function isSmartphone()
	{
		$browserIdentOverwrite = json_decode(
			\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
		);

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return in_array($browserIdentOverwrite->platform, array('smartphone', 'mobile'));
		}

		return ThemePlusEnvironment::getMobileDetect()
			->isMobile() && !ThemePlusEnvironment::getMobileDetect()
			->isTablet();
	}

	/**
	 * Shorthand check if current request is from a mobile device.
	 *
	 * @return bool
	 */
	public static function isMobile()
	{
		$browserIdentOverwrite = json_decode(
			\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
		);

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return in_array($browserIdentOverwrite->platform, array('tablet', 'smartphone', 'mobile'));
		}

		return ThemePlusEnvironment::getMobileDetect()
			->isMobile() || ThemePlusEnvironment::getMobileDetect()
			->isTablet();
	}

	/**
	 * Check filter settings.
	 *
	 * @param null   $system
	 * @param null   $browser
	 * @param string $browserVersionComparator
	 * @param null   $browserVersion
	 * @param null   $platform
	 * @param bool   $invert
	 *
	 * @return bool
	 */
	public static function checkFilter(
		$system = null,
		$browser = null,
		$browserVersionComparator = '=',
		$browserVersion = null,
		$platform = null,
		$invert = false
	) {
		$browserIdentOverwrite = json_decode(
			\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
		);

		$match = true;

		if (!empty($system)) {
			if ($browserIdentOverwrite && $browserIdentOverwrite->system) {
				$currentSystem = $browserIdentOverwrite->system;
			}
			else {
				$currentSystem = ThemePlusEnvironment::getBrowserDetect()
					->getPlatform();
			}

			$match = $match && $currentSystem == $system;
		}
		if (!empty($browser)) {
			if ($browserIdentOverwrite && $browserIdentOverwrite->browser) {
				$currentBrowser = $browserIdentOverwrite->browser;
			}
			else {
				$currentBrowser = ThemePlusEnvironment::getBrowserDetect()
					->getBrowser();
			}

			if (!empty($browserVersionComparator) && !empty($browserVersion)) {
				if ($browserIdentOverwrite && $browserIdentOverwrite->version) {
					$currentBrowserVersion = $browserIdentOverwrite->version;
				}
				else {
					$currentBrowserVersion = ThemePlusEnvironment::getBrowserDetect()
						->getVersion();
				}

				switch ($browserVersionComparator) {
					case 'lt':
						$browserVersionComparator = '<';
						break;
					case 'lte':
						$browserVersionComparator = '<=';
						break;
					case 'gte':
						$browserVersionComparator = '>=';
						break;
					case 'gt':
						$browserVersionComparator = '>';
						break;
				}

				$match = $match &&
					$currentBrowser == $browser &&
					version_compare($currentBrowserVersion, $browserVersion, $browserVersionComparator);
			}
			else {
				$match = $match && $currentBrowser == $browser;
			}
		}
		if (!empty($platform)) {
			switch ($platform) {
				case 'desktop':
					$match = $match && ThemePlusEnvironment::isDesktop();
					break;

				case 'tablet':
					$match = $match && ThemePlusEnvironment::isTablet();
					break;

				case 'smartphone':
					$match = $match && ThemePlusEnvironment::isSmartphone();
					break;

				case 'mobile':
					$match = $match && ThemePlusEnvironment::isMobile();
					break;
			}
		}

		if ($invert) {
			$match = !$match;
		}
		return $match;
	}

	/**
	 * Check the file browser filter settings against the request browser.
	 *
	 * @param \Model $file
	 *
	 * @return bool
	 */
	public static function checkBrowserFilter(\Model\Collection $file)
	{
		if ($file->filter) {
			$rules = deserialize($file->filterRule, true);

			foreach ($rules as $rule) {
				if (static::checkFilterRule($rule)) {
					return true;
				}
			}

			return false;
		}

		return true;
	}

	public static function checkFilterRule($rule)
	{
		return self::checkFilter(
			$rule['system'],
			$rule['browser'],
			$rule['comparator'],
			$rule['browser_version'],
			$rule['platform'],
			$rule['invert']
		);
	}

	/**
	 * Generate a debug comment from an asset.
	 *
	 * @return string
	 */
	public static function getDebugComment(AssetInterface $asset)
	{
		if ($GLOBALS['TL_CONFIG']['debugMode'] || ThemePlusEnvironment::isDesignerMode()) {
			return '<!-- ' . static::getAssetDebugString($asset) . ' -->' . "\n";
		}
		return '';
	}

	/**
	 * Generate a debug string for the asset.
	 *
	 * @param \Assetic\Asset\AssetInterface $asset
	 * @param string                        $depth
	 *
	 * @return string
	 */
	public static function getAssetDebugString(AssetInterface $asset, $depth = '')
	{
		$filters = array();
		foreach ($asset->getFilters() as $v) {
			$filters[] = get_class($v);
		}

		if ($asset instanceof AssetCollection) {
			/** @var AssetCollection $asset */
			$string = 'collection { ' . 'target path: ' . $asset->getTargetPath() . ', ' . 'filters: [' . implode(
					', ',
					$filters
				) . '], ' . 'last modified: ' . $asset->getLastModified();

			foreach ($asset->all() as $child) {
				$string .= "\n" . $depth . '- ' . static::getAssetDebugString(
						$child,
						$depth . '    '
					);
			}

			$string .= ' }';
			return $string;
		}

		else {
			return 'asset { ' . 'source path: ' . $asset->getSourcePath(
			) . ', ' . 'target path: ' . $asset->getTargetPath() . ', ' . 'filters: [' . implode(
				', ',
				$filters
			) . '], ' . 'last modified: ' . $asset->getLastModified() . ' }';
		}
	}

	/**
	 * Wrap the conditional comment around.
	 *
	 * @param string $html The html to wrap around.
	 * @param string $cc   The cc that should wrapped.
	 *
	 * @return string
	 */
	public static function wrapCc($html, $cc)
	{
		if (strlen($cc)) {
			return '<!--[if ' . $cc . ']>' . $html . '<![endif]-->';
		}
		return $html;
	}

	/**
	 * Strip static urls.
	 */
	public static function stripStaticURL($strUrl)
	{
		if (defined('TL_ASSETS_URL') &&
			strlen(TL_ASSETS_URL) > 0 &&
			strpos($strUrl, TL_ASSETS_URL) === 0
		) {
			return substr(
				$strUrl,
				strlen(TL_ASSETS_URL)
			);
		}
		return $strUrl;
	}

	/**
	 * Detect gzip data end decode it.
	 *
	 * @param mixed $varData
	 */
	public function decompressGzip($varData)
	{
		if ($varData[0] == 31 && $varData[0] == 139 && $varData[0] == 8
		) {
			return gzdecode($varData);
		}
		else {
			return $varData;
		}
	}


	/**
	 * Handle
	 *
	 * @charset and remove the rule.
	 */
	public function handleCharset($strContent)
	{
		if (preg_match(
			'#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui',
			$strContent,
			$arrMatch
		)
		) {
			// convert character encoding to utf-8
			if (strtoupper($arrMatch[1]) != 'UTF-8') {
				$strContent = iconv(
					strtoupper($arrMatch[1]),
					'UTF-8',
					$strContent
				);
			}
			// remove all @charset rules
			$strContent = preg_replace(
				'#\@charset\s+.*\;#Ui',
				'',
				$strContent
			);
		}
		return $strContent;
	}

	/**
	 * @see \Contao\Template::parse
	 *
	 * @param \Template $template
	 */
	public function hookParseTemplate(Template $template)
	{
		if ($template instanceof FrontendTemplate) {
			if (substr($template->getName(), 0, 3) == 'fe_') {
				$template->mootools = '[[TL_THEME_PLUS]]' . "\n" . $template->mootools;
			}
		}
	}

	/**
	 * @see \Contao\Controller::replaceDynamicScriptTags
	 *
	 * @param $strBuffer
	 */
	public function hookReplaceDynamicScriptTags($strBuffer)
	{
		global $objPage;

		if ($objPage) {
			// the search and replace array
			$sr = array();

			if (ThemePlusEnvironment::isLiveMode(
				) && $GLOBALS['TL_CONFIG']['theme_plus_compile_mode'] == 'pre-compiled'
			) {

			}

			else {
				// search for the layout
				$layout = ThemePlusEnvironment::getPageLayout();

				if ($layout) {
					$this->excludeList = array();

					// build exclude list
					if (is_array($GLOBALS['TL_THEME_EXCLUDE'])) {
						$this->excludeList = array_merge($this->excludeList, $GLOBALS['TL_THEME_EXCLUDE']);
					}
					if (!is_array($layout->theme_plus_exclude_files)) {
						$layout->theme_plus_exclude_files = deserialize(
							$layout->theme_plus_exclude_files,
							true
						);
					}
					if (count($layout->theme_plus_exclude_files) > 0) {
						foreach ($layout->theme_plus_exclude_files as $v) {
							if ($v[0]) {
								$this->excludeList[] = $v[0];
							}
						}
					}

					// parse stylesheets
					$this->parseStylesheets(
						$layout,
						$sr
					);

					// parse javascripts
					$this->parseJavaScripts(
						$layout,
						$sr
					);

					if (ThemePlusEnvironment::isDesignerMode()) {
						if (\Input::post('FORM_SUBMIT') == 'theme_plus_dev_tool') {
							$session = \Session::getInstance();

							$system   = \Input::post('theme_plus_dev_tool_system');
							$browser  = \Input::post('theme_plus_dev_tool_browser');
							$version  = \Input::post('theme_plus_dev_tool_version');
							$platform = \Input::post('theme_plus_dev_tool_platform');

							if ($system || $browser || $version || $platform) {
								$browserIdentOverwrite = (object) array(
									'system'   => $system,
									'browser'  => $browser,
									'version'  => $version,
									'platform' => $platform,
								);
								$session->set(self::BROWSER_IDENT_OVERWRITE, json_encode($browserIdentOverwrite));
							}
							else {
								$session->set(self::BROWSER_IDENT_OVERWRITE, null);
							}

							\Controller::reload();
						}

						$files             = array();
						$stylesheetsCount  = 0;
						$stylesheetsBuffer = '';
						$javascriptsCount  = 0;
						$javascriptsBuffer = '';

						foreach ($this->files as $url => $file) {
							$files[] = md5($url);

							$icon = '';
							$type = 'unknown';
							if (preg_match('#.*\.(js|css)#', $file['name'], $matches)) {
								switch ($matches[1]) {
									case 'js':
										$icon   = '<img src="system/modules/theme-plus/assets/images/javascript.png">';
										$type   = 'js';
										$buffer = & $javascriptsBuffer;
										$javascriptsCount++;
										break;
									case 'css':
										$icon   = '<img src="system/modules/theme-plus/assets/images/stylesheet.png">';
										$type   = 'css';
										$buffer = & $stylesheetsBuffer;
										$stylesheetsCount++;
										break;
								}
							}
							else {
								continue;
							}

							if ($file['url']) {
								$name = $file['url'];
							}
							else if ($file['asset'] && !$file['asset'] instanceof StringAsset) {
								$name = $file['asset']->getSourcePath();
							}
							else {
								$name = $file['name'];
							}

							$buffer .= sprintf(
								'<div id="monitor-%s" class="theme-plus-dev-tool-monitor theme-plus-dev-tool-type-%s theme-plus-dev-tool-loading">' .
								'%s ' .
								'<a href="%s" target="_blank" class="theme-plus-dev-tool-link">%s</a>' .
								'</div>
	',
								md5($url),
								$type,
								$icon,
								$url,
								$name
							);
						}

						// clean reference variable
						unset($buffer);

						$strBuffer = str_replace(
							'</head>',
							sprintf(
								'<link rel="stylesheet" href="system/modules/theme-plus/assets/css/dev.css">
	<script src="system/modules/theme-plus/assets/js/dev.js"></script>
	</head>'
							),
							$strBuffer
						);

						$browserIdentOverwrite = json_decode(
							\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
						);

						$fileDC = new File();

						$filterSystems = array('<option value="">System</option>');
						foreach ($fileDC->getSystems() as $system) {
							$filterSystems[] = sprintf(
								'<option value="%1$s"%2$s>%1$s</option>',
								$system,
								$browserIdentOverwrite && $browserIdentOverwrite->system == $system
									? ' selected'
									: ''
							);
						}

						$filterBrowsers = array('<option value="">Browser</option>');
						foreach ($fileDC->getBrowsers() as $browser) {
							$filterBrowsers[] = sprintf(
								'<option value="%1$s">%1$s</option>',
								$browser,
								$browserIdentOverwrite && $browserIdentOverwrite->browser == $browser
									? ' selected'
									: ''
							);
						}

						\Controller::loadLanguageFile('tl_theme_plus_filter');

						$filterPlatforms = array('<option value="">Platform</option>');
						foreach (array('desktop', 'tablet', 'smartphone', 'mobile') as $platform) {
							$filterPlatforms[] = sprintf(
								'<option value="%1$s"%3$s>%2$s</option>',
								$platform,
								$GLOBALS['TL_LANG']['tl_theme_plus_filter'][$platform],
								$browserIdentOverwrite && $browserIdentOverwrite->platform == $platform
									? ' selected'
									: ''
							);
						}

						$strBuffer = preg_replace(
							'|<body[^>]*>|',
							sprintf(
								'$0
	<!-- indexer::stop -->
    <div id="theme-plus-dev-tool" class="%s">
    <div id="theme-plus-dev-tool-toggler" title="Theme+ developers tool">T+</div>
    <div id="theme-plus-dev-tool-stylesheets">
        <div id="theme-plus-dev-tool-stylesheets-counter">%s <span id="theme-plus-dev-tool-stylesheets-count">0</span> / <span id="theme-plus-dev-tool-stylesheets-total">%d</span></div>
        <div id="theme-plus-dev-tool-stylesheets-files">%s</div>
    </div>
    <div id="theme-plus-dev-tool-javascripts">
        <div id="theme-plus-dev-tool-javascripts-counter">%s <span id="theme-plus-dev-tool-javascripts-count">0</span> / <span id="theme-plus-dev-tool-javascripts-total">%d</span></div>
        <div id="theme-plus-dev-tool-javascripts-files">%s</div>
    </div>
    <div id="theme-plus-dev-tool-filter">
        <form method="post" action="%s">
            <input type="hidden" name="REQUEST_TOKEN" value="%s">
            <input type="hidden" name="FORM_SUBMIT" value="theme_plus_dev_tool">
            <select id="theme-plus-dev-tool-filter-system" name="theme_plus_dev_tool_system" onchange="this.form.submit()">%s</select>
            <select id="theme-plus-dev-tool-filter-browser" name="theme_plus_dev_tool_browser" onchange="this.form.submit()">%s</select>
            <input id="theme-plus-dev-tool-filter-version" name="theme_plus_dev_tool_version" value="%s" placeholder="Version" size="4">
            <select id="theme-plus-dev-tool-filter-platform" name="theme_plus_dev_tool_platform" onchange="this.form.submit()">%s</select>
            &nbsp;
            <input id="theme-plus-dev-tool-filter-apply" type="submit" value="&raquo;">
        </form>
    </div>
    <div id="theme-plus-dev-tool-exception"></div>
    </div>
	<script>initThemePlusDevTool(%s, %s);</script>
	<!-- indexer::continue -->',
								\Input::cookie('THEME_PLUS_DEV_TOOL_COLLAPES') == 'no'
									? ''
									: 'theme-plus-dev-tool-collapsed',
								\Image::getHtml('system/modules/theme-plus/assets/images/stylesheet.png'),
								$stylesheetsCount,
								$stylesheetsBuffer,
								\Image::getHtml('system/modules/theme-plus/assets/images/javascript.png'),
								$javascriptsCount,
								$javascriptsBuffer,
								\Environment::get('request'),
								REQUEST_TOKEN,
								implode('', $filterSystems),
								implode('', $filterBrowsers),
								$browserIdentOverwrite ? $browserIdentOverwrite->version : '',
								implode('', $filterPlatforms),
								json_encode($files),
								json_encode((bool) $layout->theme_plus_javascript_lazy_load)
							),
							$strBuffer
						);
					}
				}
			}

			// replace dynamic scripts
			return str_replace(
				array_keys($sr),
				array_values($sr),
				$strBuffer
			);
		}

		return $strBuffer;
	}

	/**
	 * Parse all stylesheets and add them to the search and replace array.
	 *
	 * @param \LayoutModel $layout
	 * @param array        $sr The search and replace array.
	 *
	 * @return mixed
	 */
	protected function parseStylesheets(\LayoutModel $layout, array &$sr)
	{
		global $objPage;

		// html mode
		$xhtml     = ($objPage->outputFormat == 'xhtml');
		$tagEnding = $xhtml
			? ' />'
			: '>';

		// default filter
		$defaultFilters = AsseticFactory::createFilterOrChain(
			$layout->asseticStylesheetFilter,
			ThemePlusEnvironment::isDesignerMode()
		);

		// list of non-static stylesheets
		$stylesheets = array();

		// collection of static stylesheets
		$collection = new AssetCollection(array(), array(), TL_ROOT);

		// Add the CSS framework style sheets
		if (is_array($GLOBALS['TL_FRAMEWORK_CSS']) && !empty($GLOBALS['TL_FRAMEWORK_CSS'])) {
			$this->addAssetsToCollectionFromArray(
				array_unique($GLOBALS['TL_FRAMEWORK_CSS']),
				'css',
				null,
				$collection,
				$stylesheets,
				$defaultFilters
			);
		}
		$GLOBALS['TL_FRAMEWORK_CSS'] = array();

		// Add the internal style sheets
		if (is_array($GLOBALS['TL_CSS']) && !empty($GLOBALS['TL_CSS'])) {
			$this->addAssetsToCollectionFromArray(
				$GLOBALS['TL_CSS'],
				'css',
				true,
				$collection,
				$stylesheets,
				$defaultFilters
			);
		}
		$GLOBALS['TL_CSS'] = array();

		// Add the user style sheets
		if (is_array($GLOBALS['TL_USER_CSS']) && !empty($GLOBALS['TL_USER_CSS'])) {
			$this->addAssetsToCollectionFromArray(
				array_unique($GLOBALS['TL_USER_CSS']),
				'css',
				true,
				$collection,
				$stylesheets,
				$defaultFilters
			);
		}
		$GLOBALS['TL_USER_CSS'] = array();

		// Add layout files
		$stylesheet = StyleSheetModel::findByPks(
			deserialize(
				$layout->theme_plus_stylesheets,
				true
			),
			array('order' => 'sorting')
		);
		if ($stylesheet) {
			$this->addAssetsToCollectionFromDatabase(
				$stylesheet,
				'css',
				$collection,
				$stylesheets,
				$defaultFilters
			);
		}

		// Add files from page tree
		$this->addAssetsToCollectionFromPageTree(
			$objPage,
			'stylesheets',
			'Bit3\Contao\ThemePlus\Model\StylesheetModel',
			$collection,
			$stylesheets,
			$defaultFilters,
			true
		);

		// string contains the scripts include code
		$scripts = '';

		// add collection to list
		if (count($collection->all())) {
			$stylesheets[] = array('asset' => $collection);
		}

		// add files
		foreach ($stylesheets as $stylesheet) {
			// use proxy for development
			if (ThemePlusEnvironment::isDesignerMode() && isset($stylesheet['id'])) {
				/** @var AssetInterface $asset */
				$asset = $stylesheet['asset'];

				$id = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);

				$session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

				if (
					!$session ||
					!$session->asset instanceof StringAsset ||
					$asset->getLastModified() > $session->asset->getLastModified()
				) {
					$session          = new \stdClass;
					$session->page    = $objPage->id;
					$session->asset   = $asset;
					$session->filters = $defaultFilters;

					if ($asset instanceof StringAsset) {
						$asset->load();
					}

					$_SESSION['THEME_PLUS_ASSETS'][$id] = serialize($session);
				}

				$pathinfo = pathinfo($stylesheet['name']);

				$url               = sprintf(
					'system/modules/theme-plus/web/proxy.php/css/%s',
					$pathinfo['filename'] . '.' . $id . '.' . $pathinfo['extension']
				);
				$this->files[$url] = $stylesheet;
			}

			// use asset
			else if (isset($stylesheet['asset'])) {
				$url               = static::storeAsset(
					$stylesheet['asset'],
					'css',
					$defaultFilters
				);
				$url               = \Controller::addStaticUrlTo($url);
				$this->files[$url] = $stylesheet;
			}

			// use url
			else if (isset($stylesheet['url'])) {
				$url               = $stylesheet['url'];
				$url               = \Controller::addStaticUrlTo($url);
				$this->files[$url] = $stylesheet;
			}

			// continue if file have no source
			else {
				continue;
			}

			// generate html
			$html = '<link' .
				($xhtml
					? ' type="text/css"'
					: '') .
				' rel="stylesheet" href="' . $url . '"' .
				((isset($stylesheet['media']) && $stylesheet['media'] != 'all')
					? ' media="' . $stylesheet['media'] . '"'
					: '') .
				(ThemePlusEnvironment::isDesignerMode()
					? ' id="' . md5($url) . '"'
					: '') .
				$tagEnding;

			// wrap cc around
			$html = static::wrapCc(
					$html,
					$stylesheet['cc']
				) . "\n";

			// add debug information
			if (ThemePlusEnvironment::isDesignerMode()) {
				// use asset
				if (isset($stylesheet['asset'])) {
					$html = static::getDebugComment($stylesheet['asset']) . $html;
				}

				// use url
				else if (isset($stylesheet['url'])) {
					$html = '<!-- url { ' . $stylesheet['url'] . ' } -->' . "\n" . $html;
				}
			}

			$scripts .= $html;
		}

		$scripts .= '[[TL_CSS]]';
		$sr['[[TL_CSS]]'] = $scripts;
	}

	/**
	 * Parse all javascripts and add them to the search and replace array.
	 *
	 * @param \LayoutModel $layout
	 * @param array        $sr The search and replace array.
	 *
	 * @return mixed
	 */
	protected function parseJavaScripts(\LayoutModel $layout, array &$sr)
	{
		global $objPage;

		// html mode
		$xhtml = ($objPage->outputFormat == 'xhtml');

		// default filter
		$defaultFilters = AsseticFactory::createFilterOrChain(
			$layout->asseticJavaScriptFilter,
			ThemePlusEnvironment::isDesignerMode()
		);

		// list of non-static javascripts
		$javascripts = array();

		// collection of static javascript
		$collection = new AssetCollection(array(), array(), TL_ROOT);

		// Add the internal scripts
		if (is_array($GLOBALS['TL_JAVASCRIPT']) && !empty($GLOBALS['TL_JAVASCRIPT'])) {
			$this->addAssetsToCollectionFromArray(
				$GLOBALS['TL_JAVASCRIPT'],
				'js',
				false,
				$collection,
				$javascripts,
				$defaultFilters,
				$layout->theme_plus_default_javascript_position
			);
		}
		$GLOBALS['TL_JAVASCRIPT'] = array();

		// Add layout files
		$javascript = JavaScriptModel::findByPks(
			deserialize(
				$layout->theme_plus_javascripts,
				true
			),
			array('order' => 'sorting')
		);
		if ($javascript) {
			$this->addAssetsToCollectionFromDatabase(
				$javascript,
				'js',
				$collection,
				$javascripts,
				$defaultFilters,
				$layout->theme_plus_default_javascript_position
			);
		}

		// Add files from page tree
		$this->addAssetsToCollectionFromPageTree(
			$objPage,
			'javascripts',
			'Bit3\Contao\ThemePlus\Model\JavaScriptModel',
			$collection,
			$javascripts,
			$defaultFilters,
			true,
			$layout->theme_plus_default_javascript_position
		);

		// string contains the scripts include code
		$head = '';
		$body = '';

		// add collection to list
		if (count($collection->all())) {
			$javascripts[] = array(
				'asset'    => $collection,
				'position' => $layout->theme_plus_default_javascript_position
			);
		}

		// add files
		foreach ($javascripts as $javascript) {
			// use proxy for development
			if (ThemePlusEnvironment::isDesignerMode() && isset($javascript['id'])) {
				/** @var AssetInterface $asset */
				$asset = $javascript['asset'];

				$id = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);

				$session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

				if (
					!$session ||
					!$session->asset instanceof StringAsset ||
					$asset->getLastModified() > $session->asset->getLastModified()
				) {
					$session          = new \stdClass;
					$session->page    = $objPage->id;
					$session->asset   = $asset;
					$session->filters = $defaultFilters;

					if ($asset instanceof StringAsset) {
						$asset->load();
					}

					$_SESSION['THEME_PLUS_ASSETS'][$id] = serialize($session);
				}

				$pathinfo = pathinfo($javascript['name']);

				$url               = sprintf(
					'system/modules/theme-plus/web/proxy.php/js/%s',
					$pathinfo['filename'] . '.' . $id . '.' . $pathinfo['extension']
				);
				$this->files[$url] = $javascript;
			}

			// use asset
			else if (isset($javascript['asset'])) {
				$url               = static::storeAsset(
					$javascript['asset'],
					'js',
					$defaultFilters
				);
				$url               = \Controller::addStaticUrlTo($url);
				$this->files[$url] = $javascript;
			}

			// use url
			else if (isset($javascript['url'])) {
				$url               = $javascript['url'];
				$url               = \Controller::addStaticUrlTo($url);
				$this->files[$url] = $javascript;
			}

			// continue if file have no source
			else {
				continue;
			}

			// generate html
			if ($layout->theme_plus_javascript_lazy_load) {
				$html = '<script' .
					($xhtml
						? ' type="text/javascript"'
						: '') .
					(ThemePlusEnvironment::isDesignerMode()
						? ' id="' . md5($url) . '"'
						: '') .
					'>window.loadAsync(' .
					json_encode($url) .
					(ThemePlusEnvironment::isDesignerMode() ? ', ' . json_encode(md5($url)) : '') .
					');</script>';
			}
			else {
				$html = '<script' .
					($xhtml
						? ' type="text/javascript"'
						: '') .
					(ThemePlusEnvironment::isDesignerMode()
						? ' id="' . md5($url) . '"'
						: '') .
					' src="' . $url . '"' .
					(ThemePlusEnvironment::isDesignerMode() ? sprintf(
						' onload="window.themePlusDevTool && window.themePlusDevTool.triggerAsyncLoad(this, \'%s\');"',
						md5($url)
					) : '') .
					'></script>';
			}

			// wrap cc
			$html = static::wrapCc(
					$html,
					$javascript['cc']
				) . "\n";

			// add debug information
			if (ThemePlusEnvironment::isDesignerMode()) {
				if (isset($javascript['asset'])) {
					$html = static::getDebugComment($javascript['asset']) . $html;
				}
				else if (isset($javascript['url'])) {
					$html = '<!-- url { ' . $javascript['url'] . ' } -->' . "\n" . $html;
				}
			}

			if (isset($javascript['position']) && $javascript['position'] == 'body') {
				$body .= $html;
			}
			else {
				$head .= $html;
			}
		}

		// add async.js script
		if ($layout->theme_plus_javascript_lazy_load) {
			$async = new FileAsset(TL_ROOT . '/system/modules/theme-plus/assets/js/async.js', $defaultFilters);
			$async->setTargetPath($this->getAssetPath($async, 'js'));
			$async = '<script' . ($xhtml
					? ' type="text/javascript"'
					: '') . '>' . $async->dump() . '</script>' . "\n";

			if ($head) {
				$head = $async . $head;
			}
			else if ($body) {
				$body = $async . $body;
			}
		}

		$head .= '[[TL_HEAD]]';
		$sr['[[TL_HEAD]]'] = $head;

		$sr['[[TL_THEME_PLUS]]'] = $body;
	}

	protected function addAssetsToCollectionFromArray(
		array $sources,
		$type,
		$split,
		AssetCollection $collection,
		array &$array,
		$defaultFilters,
		$defaultPosition = 'head'
	) {
		foreach ($sources as $source) {
			if ($source instanceof AssetInterface) {
				if (ThemePlusEnvironment::isLiveMode()) {
					$collection->add($source);
				}
				else if ($source instanceof StringAsset) {
					$data = $source->dump();
					$data = gzcompress($data, 9);
					$data = base64_encode($data);

					$array[] = array(
						'id'       => $type . ':' . 'base64:' . $data,
						'name'     => 'string' . substr(md5($data), 0, 8) . '.' . $type,
						'time'     => substr(md5($data), 0, 8),
						'asset'    => $source,
						'position' => $defaultPosition
					);
				}
				else if ($source instanceof FileAsset) {
					$reflectionClass = new \ReflectionClass('Assetic\Asset\BaseAsset');
					$sourceProperty  = $reflectionClass->getProperty('sourcePath');
					$sourceProperty->setAccessible(true);
					$sourcePath = $sourceProperty->getValue($source);

					if (in_array($sourcePath, $this->excludeList)) {
						continue;
					}

					$array[]                       = array(
						'id'       => $type . ':asset:' . spl_object_hash($source),
						'name'     => basename($sourcePath, '.' . $type) . '.' . $type,
						'time'     => filemtime($sourcePath),
						'asset'    => $source,
						'position' => $defaultPosition
					);
					$this->excludeList[] = $sourcePath;
				}
				else {
					$name = get_class($source);
					$name = strtolower($name);
					$name = preg_replace('~^.*\\\\~', '', $name);
					$name = preg_replace('~asset$~', '', $name);
					$name .= '_' . standardize($source->getSourcePath());

					$array[] = array(
						'id'       => $type . ':asset:' . spl_object_hash($source),
						'name'     => $name . '.' . $type,
						'time'     => time(),
						'asset'    => $source,
						'position' => $defaultPosition
					);
				}
				continue;
			}

			if ($split === null) {
				// use source as source
			}
			else if ($split === true) {
				list($source, $media, $mode) = explode(
					'|',
					$source
				);
			}
			else if ($split === false) {
				list($source, $mode) = explode(
					'|',
					$source
				);
			}
			else {
				return;
			}

			// remove static url
			$source = static::stripStaticURL($source);

			// skip file
			if (in_array(
				$source,
				$this->excludeList
			)
			) {
				continue;
			}

			$this->excludeList[] = $source;

			// if stylesheet is an absolute url...
			if (preg_match(
				'#^\w+:#',
				$source
			)
			) {
				// ...fetch the stylesheet
				if ($mode == 'static' && ThemePlusEnvironment::isLiveMode()) {
					$asset = new HttpAsset($source);
					$asset->setTargetPath($this->getAssetPath($asset, $type));
				}
				// ...or add if it is not static
				else {
					$array[] = array(
						'url'   => $source,
						'name'  => basename($source),
						'time'  => time(),
						'media' => $media
					);
					continue;
				}
			}
			else if ($source) {
				$asset = new FileAsset(TL_ROOT . '/' . $source, $defaultFilters, TL_ROOT, $source);
				$asset->setTargetPath($this->getAssetPath($asset, $type));
			}
			else {
				continue;
			}

			if (($mode == 'static' || $mode === null) && ThemePlusEnvironment::isLiveMode()) {
				$collection->add($asset);
			}
			else {
				$array[] = array(
					'id'       => $type . ':' . $source,
					'name'     => basename($source),
					'time'     => filemtime($source),
					'asset'    => $asset,
					'media'    => $media,
					'position' => $defaultPosition
				);
			}
		}
	}

	protected function addAssetsToCollectionFromDatabase(
		\Model\Collection $data,
		$type,
		AssetCollection $collection,
		array &$array,
		$defaultFilters,
		$defaultPosition = 'head'
	) {
		if ($data) {
			while ($data->next()) {
				if (static::checkBrowserFilter($data)) {
					$asset  = null;
					$filter = array();

					if ($data->asseticFilter) {
						$temp = AsseticFactory::createFilterOrChain(
							$data->asseticFilter,
							ThemePlusEnvironment::isDesignerMode()
						);
						if ($temp) {
							$filter = array($temp);
						}
					}

					$filter[] = $defaultFilters;

					if ($data->position) {
						$position = $data->position;
					}
					else {
						$position = $defaultPosition;
					}

					switch ($data->type) {
						case 'code':
							$name  = ($data->code_snippet_title
									? $data->code_snippet_title
									: ('string' . substr(
											md5($data->code),
											0,
											8
										))) . '.' . $type;
							$time  = $data->tstamp;
							$asset = new StringAsset(
								$data->code,
								$filter,
								TL_ROOT,
								'assets/' . $type . '/' . $data->code_snippet_title . '.' . $type
							);
							$asset->setTargetPath($this->getAssetPath($asset, $type));
							$asset->setLastModified($data->tstamp);
							break;

						case 'url':
							// skip file
							if (in_array(
								$data->url,
								$this->excludeList
							)
							) {
								break;
							}

							$this->excludeList[] = $data->url;

							$name = basename($data->url);
							$time = $data->tstamp;
							if ($data->fetchUrl) {
								$asset = new HttpAsset($data->url, $filter);
								$asset->setTargetPath($this->getAssetPath($asset, $type));
							}
							else {
								$array[] = array(
									'name'     => $name,
									'url'      => $data->url,
									'media'    => $data->media,
									'cc'       => $data->cc,
									'position' => $position
								);
							}
							break;

						case 'file':
							$filepath = false;
							if ($data->filesource == $GLOBALS['TL_CONFIG']['uploadPath'] && version_compare(
									VERSION,
									'3',
									'>='
								)
							) {
								$file = (version_compare(VERSION, '3.2', '>=') ? \FilesModel::findByUuid($data->file)
									: \FilesModel::findByPk($data->file));
								if ($file) {
									$filepath = $file->path;
								}
							}
							else {
								$filepath = $data->file;
							}

							if ($filepath) {
								// skip file
								if (in_array(
									$filepath,
									$this->excludeList
								)
								) {
									break;
								}

								$this->excludeList[] = $filepath;

								$name  = basename($filepath, '.' . $type) . '.' . $type;
								$time  = filemtime($filepath);
								$asset = new FileAsset(TL_ROOT . '/' . $filepath, $filter, TL_ROOT, $filepath);
								$asset->setTargetPath($this->getAssetPath($asset, $type));
							}
							break;
					}

					if ($asset) {
						if (ThemePlusEnvironment::isLiveMode() && $defaultPosition == $position) {
							$collection->add($asset);
						}
						else {
							$array[] = array(
								'id'       => $type . ':' . $data->id,
								'name'     => $name,
								'time'     => $time,
								'asset'    => $asset,
								'position' => $position
							);
						}
					}
				}
			}
		}
	}

	protected function addAssetsToCollectionFromPageTree(
		$objPage,
		$type,
		$model,
		AssetCollection $collection,
		array &$array,
		$defaultFilters,
		$local = false,
		$defaultPosition = 'head'
	) {
		// inherit from parent page
		if ($objPage->pid) {
			$objParent = \PageModel::findWithDetails($objPage->pid);
			$this->addAssetsToCollectionFromPageTree(
				$objParent,
				$type,
				$model,
				$collection,
				$array,
				$defaultFilters,
				false,
				$defaultPosition
			);
		}

		// add local (not inherited) files
		if ($local) {
			$trigger = 'theme_plus_include_' . $type . '_noinherit';

			if ($objPage->$trigger) {
				$key = 'theme_plus_' . $type . '_noinherit';

				$data = call_user_func(
					array($model, 'findByPks'),
					deserialize(
						$objPage->$key,
						true
					),
					array('order' => 'sorting')
				);
				if ($data) {
					$this->addAssetsToCollectionFromDatabase(
						$data,
						$type == 'stylesheets'
							? 'css'
							: 'js',
						$collection,
						$array,
						$defaultFilters,
						$defaultPosition
					);
				}
			}
		}

		// add inherited files
		$trigger = 'theme_plus_include_' . $type;

		if ($objPage->$trigger) {
			$key = 'theme_plus_' . $type;

			$data = call_user_func(
				array($model, 'findByPks'),
				deserialize(
					$objPage->$key,
					true
				),
				array('order' => 'sorting')
			);
			if ($data) {
				$this->addAssetsToCollectionFromDatabase(
					$data,
					$type == 'stylesheets'
						? 'css'
						: 'js',
					$collection,
					$array,
					$defaultFilters,
					$defaultPosition
				);
			}
		}
	}

	/**
	 * Render a variable to css code.
	 */
	static public function renderVariable(VariableModel $variable)
	{
		// HOOK: create framework code
		if (isset($GLOBALS['TL_HOOKS']['renderVariable']) && is_array($GLOBALS['TL_HOOKS']['renderVariable'])) {
			foreach ($GLOBALS['TL_HOOKS']['renderVariable'] as $callback) {
				$object    = \System::importStatic($callback[0]);
				$varResult = $object->$callback[1]($variable);
				if ($varResult !== false) {
					return $varResult;
				}
			}
		}

		switch ($variable->type) {
			case 'text':
				return $variable->text;

			case 'url':
				return sprintf(
					'url("%s")',
					str_replace(
						'"',
						'\\"',
						$variable->url
					)
				);

			case 'file':
				return sprintf(
					'url("../../%s")',
					str_replace(
						'"',
						'\\"',
						$variable->file
					)
				);

			case 'color':
				return '#' . $variable->color;

			case 'size':
				$arrSize       = deserialize($variable->size);
				$arrTargetSize = array();
				foreach (array('top', 'right', 'bottom', 'left') as $k) {
					if (strlen($arrSize[$k])) {
						$arrTargetSize[] = $arrSize[$k] . $arrSize['unit'];
					}
					else {
						$arrTargetSize[] = '';
					}
				}
				while (count($arrTargetSize) > 0 && empty($arrTargetSize[count($arrTargetSize) - 1])) {
					array_pop($arrTargetSize);
				}
				foreach ($arrTargetSize as $k => $v) {
					if (empty($v)) {
						$arrTargetSize[$k] = '0';
					}
				}
				return implode(
					' ',
					$arrTargetSize
				);
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

			$objVariable = \Database::getInstance()
				->prepare("SELECT * FROM tl_theme_plus_variable WHERE pid=?")
				->execute($objTheme->id);

			while ($objVariable->next()) {
				$this->arrVariables[$objTheme->id][$objVariable->name] = $this->renderVariable(
					$objVariable,
					$strPath
				);
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
			$arrVariables = $this->getVariables(
				false,
				$strPath
			);
		}
		$objVariableReplace = new VariableReplacer($arrVariables);
		return preg_replace_callback(
			'#\$([[:alnum:]_\-]+)#',
			array(&$objVariableReplace, 'replace'),
			$strCode
		);
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariablesByTheme($strCode, $varTheme, $strPath = false)
	{
		$objVariableReplace = new VariableReplacer($this->getVariables(
			$varTheme,
			$strPath
		));
		return preg_replace_callback(
			'#\$([[:alnum:]_\-]+)#',
			array(&$objVariableReplace, 'replace'),
			$strCode
		);
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariablesByLayout($strCode, $varLayout, $strPath = false)
	{
		$objVariableReplace = new VariableReplacer($this->getVariables(
			$this->findThemeByLayout($varLayout),
			$strPath
		));
		return preg_replace_callback(
			'#\$([[:alnum:]_\-]+)#',
			array(&$objVariableReplace, 'replace'),
			$strCode
		);
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHash($arrVariables)
	{
		$strVariables = '';
		foreach ($arrVariables as $k => $v) {
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
	 * Wrap a javascript src for lazy include.
	 *
	 * @return string
	 */
	public function wrapJavaScriptLazyInclude($strSrc)
	{
		return 'loadAsync(' . json_encode($strSrc) . (ThemePlus::getInstance()
			->isDesignerMode() ? ', ' . json_encode(md5($strSrc)) : '') . ');';
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
	 * Generate the html code.
	 *
	 * @param array  $arrFileIds
	 * @param bool   $blnAbsolutizeUrls
	 * @param object $objAbsolutizePage
	 *
	 * @return string
	 */
	public function includeFiles(
		$arrFileIds,
		$blnAggregate = null,
		$blnAbsolutizeUrls = false,
		$objAbsolutizePage = null
	) {
		$arrResult = array();

		// add css files
		$arrFiles = $this->getCssFiles(
			$arrFileIds,
			$blnAggregate,
			$blnAbsolutizeUrls,
			$objAbsolutizePage
		);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getIncludeHtml();
		}

		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $objFile) {
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
		$arrFiles = $this->getCssFiles(
			$arrFileIds,
			$blnAbsolutizeUrls,
			$objAbsolutizePage
		);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getEmbeddedHtml();
		}

		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getEmbeddedHtml();
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
		$arrParts = explode(
			'::',
			$strTag
		);
		$arrIds   = explode(
			',',
			$arrParts[1]
		);
		switch ($arrParts[0]) {
			case 'include_theme_file':
				return implode(
					"\n",
					$this->includeFiles($arrIds)
				) . "\n";

			case 'embed_theme_file':
				return implode(
					"\n",
					$this->embedFiles($arrIds)
				) . "\n";

			// @deprecated
			case 'insert_additional_sources':
				return implode(
					"\n",
					$this->includeFiles($arrIds)
				) . "\n";

			// @deprecated
			case 'include_additional_sources':
				return implode(
					"\n",
					$this->embedFiles($arrIds)
				) . "\n";
		}

		return false;
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
		$a = array_search(
			$a,
			$this->arrSortedIds
		);
		$b = array_search(
			$b,
			$this->arrSortedIds
		);

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
class VariableReplacer
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
		if (isset($GLOBALS['TL_HOOKS']['replaceUndefinedVariable']) && is_array(
				$GLOBALS['TL_HOOKS']['replaceUndefinedVariable']
			)
		) {
			foreach ($GLOBALS['TL_HOOKS']['replaceUndefinedVariable'] as $callback) {
				$object    = \System::importStatic($callback[0]);
				$varResult = $object->$callback[1]($m[1]);
				if ($varResult !== false) {
					return $varResult;
				}
			}
		}

		return $m[0];
	}
}
