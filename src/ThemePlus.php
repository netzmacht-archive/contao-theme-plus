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

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Asset\DelegateAssetInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use FrontendTemplate;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Template;

/**
 * Class ThemePlus
 *
 * Adding files to the page layout.
 */
class ThemePlus
{
	const BROWSER_IDENT_OVERWRITE = 'THEME_PLUS_BROWSER_IDENT_OVERWRITE';

	/**
	 * @var DeveloperTool
	 */
	protected $developerTool;

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
	 * @param $buffer
	 */
	public function hookReplaceDynamicScriptTags($buffer)
	{
		global $objPage;

		if ($objPage) {
			if (ThemePlusEnvironment::isDesignerMode()) {
				$this->developerTool = new DeveloperTool();
			}

			// the search and replace array
			$sr = [
				'[[TL_CSS]]'        => '',
				'[[TL_THEME_PLUS]]' => '',
				'[[TL_HEAD]]'       => '',
			];

			// search for the layout
			$layout = \LayoutModel::findByPk($objPage->layout);

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

			/*
			$this->excludeList = [];

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
			*/

			if (ThemePlusEnvironment::isDesignerMode()) {
				$buffer = $this->developerTool->inject($buffer);
			}

			// replace dynamic scripts
			return str_replace(
				array_keys($sr),
				array_values($sr),
				$buffer
			);
		}

		return $buffer;
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

		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		// collect stylesheet assets
		$event = new CollectAssetsEvent($objPage, $layout);
		$eventDispatcher->dispatch(ThemePlusEvents::COLLECT_STYLESHEET_ASSETS, $event);

		$collection = $event->getAssets();

		if (ThemePlusEnvironment::isDesignerMode()) {
			$sr['[[TL_CSS]]'] = $this->generateStylesheetDesignerModeHtml($collection);
		}
		else {
			$combinedAssets   = new AssetCollection();
			$standaloneAssets = new AssetCollection();

			ThemePlusUtils::splitAssets($collection, $combinedAssets, $standaloneAssets);

			/** @var AsseticFactory $asseticFactory */
			$asseticFactory = $GLOBALS['container']['assetic.factory'];

			// default filter
			$defaultFilters = $asseticFactory->createFilterOrChain(
				$layout->asseticStylesheetFilter,
				ThemePlusEnvironment::isDesignerMode()
			);

			if (count($combinedAssets->all())) {
				$sr['[[TL_CSS]]'] .= $this->generateStylesheetHtml($combinedAssets, $defaultFilters);
			}

			foreach ($standaloneAssets as $asset) {
				$sr['[[TL_CSS]]'] .= $this->generateStylesheetHtml($asset, $defaultFilters);
			}
		}
	}

	/**
	 * @param AssetCollectionInterface|AssetInterface[] $collection
	 *
	 * @return string
	 */
	protected function generateStylesheetDesignerModeHtml(AssetCollectionInterface $collection)
	{
		global $objPage;

		// html mode
		$xhtml     = ($objPage->outputFormat == 'xhtml');
		$tagEnding = $xhtml ? ' />' : '>';

		$html = '';

		foreach ($collection as $asset) {
			// deep link collections
			if ($asset instanceof AssetCollectionInterface) {
				$html .= $this->generateStylesheetDesignerModeHtml($asset);
				continue;
			}

			// session id
			$id = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);

			// get the session object
			$session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

			if (!$session || $asset->getLastModified() > $session->asset->getLastModified()) {
				$session        = new \stdClass;
				$session->page  = $objPage->id;
				$session->asset = $asset;

				$_SESSION['THEME_PLUS_ASSETS'][$id] = serialize($session);
			}

			$realAssets = $asset;
			while ($realAssets instanceof DelegateAssetInterface) {
				$realAssets = $realAssets->getAsset();
			}

			if ($realAssets instanceof FileAsset) {
				$name = basename($realAssets->getSourcePath());
			}
			else if ($realAssets instanceof HttpAsset) {
				$class    = new \ReflectionClass($realAssets);
				$property = $class->getProperty('sourceUrl');
				$property->setAccessible(true);
				$url  = $property->getValue($realAssets);
				$name = 'url_' . basename(parse_url($url, PHP_URL_PATH));
			}
			else {
				$name = 'asset_' . $id;
			}

			// generate the proxy url
			$url = sprintf(
				'assets/theme-plus/proxy.php/css/%s/%s',
				$id,
				$name
			);

			// overwrite the target path
			$asset->setTargetPath($url);

			// remember asset for debug tool
			$this->developerTool->registerFile(
				$id,
				(object) [
					'asset' => $realAssets,
					'type'  => 'css',
					'url'   => $url,
				]
			);

			// generate html
			$linkHtml = '<link';
			$linkHtml .= sprintf(' id="%s"', $id);
			$linkHtml .= sprintf(' href="%s"', $url);
			if ($xhtml) {
				$linkHtml .= ' type="text/css"';
			}
			$linkHtml .= ' rel="stylesheet"';
			if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
				$linkHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
			}
			$linkHtml .= $tagEnding;

			// wrap cc around
			if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
				$linkHtml = ThemePlusUtils::wrapCc($linkHtml, $asset->getConditionalComment());
			}

			// add debug information
			$html .= ThemePlusUtils::getDebugComment($asset);

			$html .= $linkHtml . PHP_EOL;
		}

		return $html;
	}

	protected function generateStylesheetHtml(AssetInterface $asset, $defaultFilters)
	{
		$targetPath = ThemePlusUtils::getAssetPath($asset, 'css');

		if (!file_exists(TL_ROOT . DIRECTORY_SEPARATOR . $targetPath)) {
			// overwrite the target path
			$asset->setTargetPath($targetPath);

			// load and dump the collection
			$asset->load($defaultFilters);
			$css = $asset->dump($defaultFilters);

			// write the asset
			file_put_contents($targetPath, $css);
		}

		global $objPage;

		// html mode
		$xhtml     = ($objPage->outputFormat == 'xhtml');
		$tagEnding = $xhtml ? ' />' : '>';

		// generate html
		$linkHtml = '<link';
		$linkHtml .= sprintf(' href="%s"', $targetPath);
		if ($xhtml) {
			$linkHtml .= ' type="text/css"';
		}
		$linkHtml .= ' rel="stylesheet"';
		if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
			$linkHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
		}
		$linkHtml .= $tagEnding . PHP_EOL;

		// wrap cc around
		if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
			$linkHtml = ThemePlusUtils::wrapCc($linkHtml, $asset->getConditionalComment());
		}

		return $linkHtml;
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

		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		if (!ThemePlusEnvironment::isDesignerMode()) {
			/** @var AsseticFactory $asseticFactory */
			$asseticFactory = $GLOBALS['container']['assetic.factory'];

			// default filter
			$defaultFilters = $asseticFactory->createFilterOrChain(
				$layout->asseticJavaScriptFilter,
				ThemePlusEnvironment::isDesignerMode()
			);
		}
		else {
			$defaultFilters = null;
		}

		// inject async.js if required
		if ($layout->theme_plus_javascript_lazy_load) {
			$asset = new ExtendedFileAsset(TL_ROOT . '/assets/theme-plus/js/async.js', [], TL_ROOT, 'assets/theme-plus/js/async.js');
			$asset->setInline(true);

			$event = new RenderAssetHtmlEvent($objPage, $layout, $defaultFilters, $asset, $this->developerTool);
			$eventDispatcher->dispatch(ThemePlusEvents::RENDER_JAVASCRIPT_HTML, $event);

			if ($layout->theme_plus_default_javascript_position == 'body') {
				$sr['[[TL_THEME_PLUS]]'] .= $event->getHtml();
			}
			else {
				$sr['[[TL_HEAD]]'] .= $event->getHtml();
			}
		}

		// collect head javascript assets
		$event = new CollectAssetsEvent($objPage, $layout);
		$eventDispatcher->dispatch(ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS, $event);

		$collection = $event->getAssets();

		if (ThemePlusEnvironment::isDesignerMode()) {
			$sr['[[TL_HEAD]]'] = $this->generateJavaScriptDesignerModeHtml($collection, $layout);
		}
		else {
			$combinedAssets   = new AssetCollection();
			$standaloneAssets = new AssetCollection();

			ThemePlusUtils::splitAssets($collection, $combinedAssets, $standaloneAssets);

			if (count($combinedAssets->all())) {
				$sr['[[TL_HEAD]]'] .= $this->generateJavaScriptHtml($combinedAssets, $defaultFilters, $layout);
			}

			foreach ($standaloneAssets as $asset) {
				$sr['[[TL_HEAD]]'] .= $this->generateJavaScriptHtml($asset, $defaultFilters, $layout);
			}
		}

		// collect body javascript assets
		$event = new CollectAssetsEvent($objPage, $layout);
		$eventDispatcher->dispatch(ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS, $event);

		$collection = $event->getAssets();

		if (ThemePlusEnvironment::isDesignerMode()) {
			$sr['[[TL_THEME_PLUS]]'] = $this->generateJavaScriptDesignerModeHtml($collection, $layout);
		}
		else {
			$combinedAssets   = new AssetCollection();
			$standaloneAssets = new AssetCollection();

			ThemePlusUtils::splitAssets($collection, $combinedAssets, $standaloneAssets);

			if (count($combinedAssets->all())) {
				$sr['[[TL_THEME_PLUS]]'] .= $this->generateJavaScriptHtml($combinedAssets, $defaultFilters, $layout);
			}

			foreach ($standaloneAssets as $asset) {
				$sr['[[TL_THEME_PLUS]]'] .= $this->generateJavaScriptHtml($asset, $defaultFilters, $layout);
			}
		}
	}

	/**
	 * @param AssetCollectionInterface|AssetInterface[] $collection
	 *
	 * @return string
	 */
	protected function generateJavaScriptDesignerModeHtml(AssetCollectionInterface $collection, \LayoutModel $layout)
	{
		global $objPage;

		// html mode
		$xhtml = ($objPage->outputFormat == 'xhtml');

		$html = '';

		foreach ($collection as $asset) {
			// deep link collections
			if ($asset instanceof AssetCollectionInterface) {
				$html .= $this->generateStylesheetDesignerModeHtml($asset);
				continue;
			}

			// session id
			$id = substr(md5($asset->getSourceRoot() . '/' . $asset->getSourcePath()), 0, 8);

			// get the session object
			$session = unserialize($_SESSION['THEME_PLUS_ASSETS'][$id]);

			if (!$session || $asset->getLastModified() > $session->asset->getLastModified()) {
				$session        = new \stdClass;
				$session->page  = $objPage->id;
				$session->asset = $asset;

				$_SESSION['THEME_PLUS_ASSETS'][$id] = serialize($session);
			}

			$realAssets = $asset;
			while ($realAssets instanceof DelegateAssetInterface) {
				$realAssets = $realAssets->getAsset();
			}

			if ($realAssets instanceof FileAsset) {
				$name = basename($realAssets->getSourcePath());
			}
			else if ($realAssets instanceof HttpAsset) {
				$class    = new \ReflectionClass($realAssets);
				$property = $class->getProperty('sourceUrl');
				$property->setAccessible(true);
				$url  = $property->getValue($realAssets);
				$name = 'url_' . basename(parse_url($url, PHP_URL_PATH));
			}
			else {
				$name = 'asset_' . $id;
			}

			// generate the proxy url
			$url = sprintf(
				'assets/theme-plus/proxy.php/js/%s/%s',
				$id,
				$name
			);

			// overwrite the target path
			$asset->setTargetPath($url);

			// remember asset for debug tool
			$this->developerTool->registerFile(
				$id,
				(object) [
					'asset' => $realAssets,
					'type'  => 'js',
					'url'   => $url,
				]
			);

			// generate html
			if ($layout->theme_plus_javascript_lazy_load) {
				$scriptHtml = '<script';
				$scriptHtml .= sprintf(' id="%s"', $id);
				if ($xhtml) {
					$scriptHtml .= ' type="text/javascript"';
				}
				$scriptHtml .= '>';
				$scriptHtml .= sprintf(
					'var s=document.createElement("script");' .
					's.addEventListener("load",function(){window.themePlusDevTool && window.themePlusDevTool.triggerAsyncLoad(this, %s);});' .
					's.async=true;' .
					's.src=%s;' .
					'document.head.appendChild(s);',
					json_encode($id),
					json_encode($url)
				);
				$scriptHtml .= '</script>';
			}
			else {
				$scriptHtml = '<script';
				$scriptHtml .= sprintf(' id="%s"', $id);
				$scriptHtml .= sprintf(' src="%s"', $url);
				if ($xhtml) {
					$scriptHtml .= ' type="text/javascript"';
				}
				$scriptHtml .= sprintf(
					' onload="window.themePlusDevTool && window.themePlusDevTool.triggerAsyncLoad(this, \'%s\');"',
					$id
				);
				$scriptHtml .= '></script>';
			}

			// wrap cc around
			if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
				$scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
			}

			// add debug information
			$html .= ThemePlusUtils::getDebugComment($asset);

			$html .= $scriptHtml . PHP_EOL;
		}

		return $html;
	}

	protected function generateJavaScriptHtml(AssetInterface $asset, $defaultFilters, \LayoutModel $layout)
	{
		$targetPath = ThemePlusUtils::getAssetPath($asset, 'js');

		if (!file_exists(TL_ROOT . DIRECTORY_SEPARATOR . $targetPath)) {
			// overwrite the target path
			$asset->setTargetPath($targetPath);

			// load and dump the collection
			$asset->load($defaultFilters);
			$css = $asset->dump($defaultFilters);

			// write the asset
			file_put_contents($targetPath, $css);
		}

		global $objPage;

		// html mode
		$xhtml = ($objPage->outputFormat == 'xhtml');

		// generate html
		if ($layout->theme_plus_javascript_lazy_load) {
			$scriptHtml = '<script';
			if ($xhtml) {
				$scriptHtml .= ' type="text/javascript"';
			}
			$scriptHtml .= '>';
			$scriptHtml .= sprintf(
				'var s=document.createElement("script");' .
				's.async=true;' .
				's.src=%s;' .
				'document.head.appendChild(s);',
				json_encode($targetPath)
			);
			$scriptHtml .= '</script>';
		}
		else {
			$scriptHtml = '<script';
			$scriptHtml .= sprintf(' src="%s"', $targetPath);
			if ($xhtml) {
				$scriptHtml .= ' type="text/javascript"';
			}
			$scriptHtml .= '></script>';
		}

		// wrap cc around
		if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
			$scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
		}

		return $scriptHtml;
	}
}
