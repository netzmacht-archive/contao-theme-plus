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

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Bit3\Contao\ThemePlus\Asset\DelegateAssetInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JavaScriptRenderer implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::RENDER_JAVASCRIPT_HTML => [
				['renderDesignerModeHtml'],
				['renderDesignerModeInlineHtml'],
				['renderLinkHtml'],
				['renderInlineHtml'],
			],
		];
	}

	public function renderDesignerModeHtml(RenderAssetHtmlEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
	{
		if (!$event->getHtml() && $event->getDeveloperTool()) {
			$asset = $event->getAsset();

			if (!$asset instanceof ExtendedAssetInterface || !$asset->isInline()) {
				global $objPage;

				$html = '';

				// html mode
				$xhtml = ($objPage->outputFormat == 'xhtml');

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
				$event->getDeveloperTool()->registerFile(
					$id,
					(object) [
						'asset' => $realAssets,
						'type'  => 'js',
						'url'   => $url,
					]
				);

				// generate html
				if ($event->getLayout()->theme_plus_javascript_lazy_load) {
					$scriptHtml = '<script';
					$scriptHtml .= sprintf(' id="%s"', $id);
					if ($xhtml) {
						$scriptHtml .= ' type="text/javascript"';
					}
					$scriptHtml .= '>';
					$scriptHtml .= sprintf(
						'window.loadAsync(%s, %s)',
						json_encode($url),
						json_encode($id)
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

				$event->setHtml($html);
			}
		}
	}

	public function renderDesignerModeInlineHtml(
		RenderAssetHtmlEvent $event,
		$eventName,
		EventDispatcherInterface $eventDispatcher
	) {
		if (!$event->getHtml() && $event->getDeveloperTool()) {
			$asset = $event->getAsset();

			if ($asset instanceof ExtendedAssetInterface && $asset->isInline()) {
				global $objPage;

				$html = '';

				// html mode
				$xhtml = ($objPage->outputFormat == 'xhtml');

				// retrieve page path
				$targetPath = \Environment::get('requestUri');
				// remove query string
				$targetPath = preg_replace('~\?\.*~', '', $targetPath);
				// remove leading /
				$targetPath = ltrim($targetPath, '/');

				// overwrite the target path
				$asset->setTargetPath($targetPath);

				// load and dump the collection
				$asset->load($event->getDefaultFilters());
				$js = $asset->dump($event->getDefaultFilters());

				// generate html
				$scriptHtml = '<script';
				if ($xhtml) {
					$scriptHtml .= ' type="text/javascript"';
				}
				$scriptHtml .= '>';
				$scriptHtml .= $js;
				$scriptHtml .= '</script>';

				// wrap cc around
				if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
					$scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
				}

				// add debug information
				$html .= ThemePlusUtils::getDebugComment($asset);

				$html .= $scriptHtml . PHP_EOL;

				$event->setHtml($html);
			}
		}
	}

	public function renderLinkHtml(RenderAssetHtmlEvent $event)
	{
		if (!$event->getHtml() && !$event->getDeveloperTool()) {
			$asset = $event->getAsset();

			if (!$asset instanceof ExtendedAssetInterface || !$asset->isInline()) {
				$targetPath = ThemePlusUtils::getAssetPath($asset, 'js');

				if (!file_exists(TL_ROOT . DIRECTORY_SEPARATOR . $targetPath)) {
					// overwrite the target path
					$asset->setTargetPath($targetPath);

					// load and dump the collection
					$asset->load($event->getDefaultFilters());
					$js = $asset->dump($event->getDefaultFilters());

					// write the asset
					file_put_contents($targetPath, $js);
				}

				global $objPage;

				// html mode
				$xhtml = ($objPage->outputFormat == 'xhtml');

				// generate html
				if ($event->getLayout()->theme_plus_javascript_lazy_load) {
					$scriptHtml = '<script';
					if ($xhtml) {
						$scriptHtml .= ' type="text/javascript"';
					}
					$scriptHtml .= '>';
					$scriptHtml .= sprintf(
							'window.loadAsync(%s)',
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
				$scriptHtml .= PHP_EOL;

				// wrap cc around
				if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
					$scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
				}

				$event->setHtml($scriptHtml);
			}
		}
	}

	public function renderInlineHtml(RenderAssetHtmlEvent $event)
	{
		if (!$event->getHtml() && !$event->getDeveloperTool()) {
			$asset = $event->getAsset();

			if ($asset instanceof ExtendedAssetInterface && $asset->isInline()) {
				// retrieve page path
				$targetPath = \Environment::get('requestUri');
				// remove query string
				$targetPath = preg_replace('~\?\.*~', '', $targetPath);
				// remove leading /
				$targetPath = ltrim($targetPath, '/');

				// overwrite the target path
				$asset->setTargetPath($targetPath);

				// load and dump the collection
				$asset->load($event->getDefaultFilters());
				$js = $asset->dump($event->getDefaultFilters());

				global $objPage;

				// html mode
				$xhtml = ($objPage->outputFormat == 'xhtml');

				// generate html
				$scriptHtml = '<script';
				if ($xhtml) {
					$scriptHtml .= ' type="text/javascript"';
				}
				$scriptHtml .= '>';
				$scriptHtml .= $js;
				$scriptHtml .= '</script>';
				$scriptHtml .= PHP_EOL;

				// wrap cc around
				if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
					$scriptHtml = ThemePlusUtils::wrapCc($scriptHtml, $asset->getConditionalComment());
				}

				$event->setHtml($scriptHtml);
			}
		}
	}
}
