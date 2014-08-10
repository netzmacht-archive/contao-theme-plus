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

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssRewriteFilter;
use Bit3\Contao\ThemePlus\Asset\DatabaseAsset;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Model\StylesheetModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StylesheetCollector implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::COLLECT_STYLESHEET_ASSETS => [
				['collectFrameworkStylesheets'],
				['collectRuntimeStylesheets'],
				['collectLayoutStylesheets'],
				['collectPageStylesheets'],
				['collectUserStylesheets'],
			],
		];
	}

	public function collectFrameworkStylesheets(CollectAssetsEvent $event)
	{
		if (is_array($GLOBALS['TL_FRAMEWORK_CSS']) && !empty($GLOBALS['TL_FRAMEWORK_CSS'])) {
			foreach (array_unique($GLOBALS['TL_FRAMEWORK_CSS']) as $stylesheet) {
				$asset = new FileAsset(TL_ROOT . DIRECTORY_SEPARATOR . $stylesheet, [new CssRewriteFilter()], TL_ROOT, $stylesheet);
				$asset->setTargetPath(ThemePlusUtils::getAssetPath($asset, 'css'));
				$event->append($asset, -50);
			}

			$GLOBALS['TL_FRAMEWORK_CSS'] = [];
		}
	}

	public function collectRuntimeStylesheets(CollectAssetsEvent $event)
	{
		if (is_array($GLOBALS['TL_CSS']) && !empty($GLOBALS['TL_CSS'])) {
			foreach ($GLOBALS['TL_CSS'] as $stylesheet) {
				if ($stylesheet instanceof AssetInterface) {
					$event->append($stylesheet);
				}
				else {
					list($source, $media, $mode) = explode('|', $stylesheet);

					$asset = new ExtendedFileAsset(TL_ROOT . '/' . $source, [new CssRewriteFilter()], TL_ROOT, $stylesheet);
					$asset->setTargetPath(ThemePlusUtils::getAssetPath($asset, 'css'));
					$asset->setMediaQuery($media);
					$asset->setStandalone($mode != 'static');
					$event->append($asset);
				}
			}

			$GLOBALS['TL_CSS'] = [];
		}
	}

	public function collectLayoutStylesheets(CollectAssetsEvent $event)
	{
		$stylesheets = StylesheetModel::findByPks(
			deserialize(
				$event->getLayout()->theme_plus_stylesheets,
				true
			),
			['order' => 'sorting']
		);
		if ($stylesheets) {
			foreach ($stylesheets as $stylesheet) {
				$asset = new DatabaseAsset($stylesheet->row(), 'css');
				$event->append($asset, 50);
			}
		}
	}

	public function collectPageStylesheets(CollectAssetsEvent $event)
	{
		$page          = $event->getPage();
		$stylesheetIds = [];

		// add noinherit stylesheets from current page
		if ($page->theme_plus_include_stylesheets_noinherit) {
			$stylesheetIds = deserialize(
				$page->theme_plus_stylesheets_noinherit,
				true
			);
		}

		// add inherited stylesheets from page trail
		while ($page) {
			if ($page->theme_plus_include_stylesheets) {
				$stylesheetIds = array_merge(
					$stylesheetIds,
					deserialize(
						$page->theme_plus_stylesheets,
						true
					)
				);
			}

			$page = \PageModel::findByPk($page->pid);
		}

		$stylesheets = StylesheetModel::findByPks(
			$stylesheetIds,
			['order' => 'sorting']
		);
		if ($stylesheets) {
			foreach ($stylesheets as $stylesheet) {
				$asset = new DatabaseAsset($stylesheet->row(), 'css');
				$event->append($asset, 100);
			}
		}
	}

	public function collectUserStylesheets(CollectAssetsEvent $event)
	{
		if (is_array($GLOBALS['TL_USER_CSS']) && !empty($GLOBALS['TL_USER_CSS'])) {
			foreach ($GLOBALS['TL_USER_CSS'] as $stylesheet) {
				if ($stylesheet instanceof AssetInterface) {
					$event->append($stylesheet);
				}
				else {
					list($source, $media, $mode, $version) = explode('|', $stylesheet);

					$asset = new ExtendedFileAsset(TL_ROOT . '/' . $source, [new CssRewriteFilter()], TL_ROOT, $stylesheet);
					$asset->setTargetPath(ThemePlusUtils::getAssetPath($asset, 'css'));
					$asset->setMediaQuery($media);
					$asset->setStandalone($mode != 'static');
					$event->append($asset, 150);
				}
			}

			$GLOBALS['TL_USER_CSS'] = [];
		}
	}
}
