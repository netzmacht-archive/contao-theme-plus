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

class StylesheetRenderer implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::RENDER_STYLESHEET_HTML => [
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
				$xhtml     = ($objPage->outputFormat == 'xhtml');
				$tagEnding = $xhtml ? ' />' : '>';

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
				$event->getDeveloperTool()->registerFile(
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
				$css = $asset->dump($event->getDefaultFilters());

				// generate html
				$styleHtml = '<style';
				if ($xhtml) {
					$styleHtml .= ' type="text/css"';
				}
				if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
					$styleHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
				}
				$styleHtml .= '>';
				$styleHtml .= $css;
				$styleHtml .= '</style>';

				// wrap cc around
				if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
					$styleHtml = ThemePlusUtils::wrapCc($styleHtml, $asset->getConditionalComment());
				}

				// add debug information
				$html .= ThemePlusUtils::getDebugComment($asset);

				$html .= $styleHtml . PHP_EOL;

				$event->setHtml($html);
			}
		}
	}

	public function renderLinkHtml(RenderAssetHtmlEvent $event)
	{
		if (!$event->getHtml() && !$event->getDeveloperTool()) {
			$asset = $event->getAsset();

			if (!$asset instanceof ExtendedAssetInterface || !$asset->isInline()) {
				$targetPath = ThemePlusUtils::getAssetPath($asset, 'css');

				if (!file_exists(TL_ROOT . DIRECTORY_SEPARATOR . $targetPath)) {
					// overwrite the target path
					$asset->setTargetPath($targetPath);

					// load and dump the collection
					$asset->load($event->getDefaultFilters());
					$css = $asset->dump($event->getDefaultFilters());

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
				$linkHtml .= $tagEnding;

				// wrap cc around
				if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
					$linkHtml = ThemePlusUtils::wrapCc($linkHtml, $asset->getConditionalComment());
				}

				$linkHtml .= PHP_EOL;

				$event->setHtml($linkHtml);
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
				$css = $asset->dump($event->getDefaultFilters());

				global $objPage;

				// html mode
				$xhtml = ($objPage->outputFormat == 'xhtml');

				// generate html
				$styleHtml = '<style';
				if ($xhtml) {
					$styleHtml .= ' type="text/css"';
				}
				if ($asset instanceof ExtendedAssetInterface && $asset->getMediaQuery()) {
					$styleHtml .= sprintf(' media="%s"', $asset->getMediaQuery());
				}
				$styleHtml .= '>';
				$styleHtml .= $css;
				$styleHtml .= '</style>';

				// wrap cc around
				if ($asset instanceof ExtendedAssetInterface && $asset->getConditionalComment()) {
					$styleHtml = ThemePlusUtils::wrapCc($styleHtml, $asset->getConditionalComment());
				}

				$styleHtml .= PHP_EOL;

				$event->setHtml($styleHtml);
			}
		}
	}
}
