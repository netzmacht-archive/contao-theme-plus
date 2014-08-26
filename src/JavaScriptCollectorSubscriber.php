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
use Bit3\Contao\ThemePlus\Asset\DatabaseAsset;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Model\JavaScriptModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JavaScriptCollectorSubscriber implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS => [
				['collectRuntimeJavaScripts'],
				['collectLayoutJavaScripts'],
				['collectPageJavaScripts'],
			],
			ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS => [
				['collectRuntimeJavaScripts'],
				['collectLayoutJavaScripts'],
				['collectPageJavaScripts'],
			],
		];
	}

	public function collectRuntimeJavaScripts(CollectAssetsEvent $event, $eventName)
	{
		if (
			$eventName == ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS &&
			$event->getLayout()->theme_plus_default_javascript_position != 'head' ||
			$eventName == ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS &&
			$event->getLayout()->theme_plus_default_javascript_position != 'body'
		) {
			return;
		}

		if (is_array($GLOBALS['TL_JAVASCRIPT']) && !empty($GLOBALS['TL_JAVASCRIPT'])) {
			foreach ($GLOBALS['TL_JAVASCRIPT'] as $javaScript) {
				if ($javaScript instanceof AssetInterface) {
					$event->append($javaScript);
				}
				else {
					list($javaScript, $mode) = explode('|', $javaScript);

					$asset = new ExtendedFileAsset(TL_ROOT . '/' . $javaScript, [], TL_ROOT, $javaScript);
					$asset->setStandalone($mode != 'static');

					$generateAssetPathEvent = new GenerateAssetPathEvent(
						$event->getPage(),
						$event->getLayout(),
						$asset,
						'js'
					);
					$eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

					$asset->setTargetPath($generateAssetPathEvent->getPath());
					$event->append($asset);
				}
			}

			$GLOBALS['TL_JAVASCRIPT'] = [];
		}
	}

	public function collectLayoutJavaScripts(CollectAssetsEvent $event, $eventName)
	{
		$whereIds = deserialize(
			$event->getLayout()->theme_plus_javascripts,
			true
		);

		if (empty($whereIds)) {
			return;
		}

		$columns = ['(' . implode(' OR ', array_fill(0, count($whereIds), 'id=?')) . ')'];
		$values  = $whereIds;

		if ($eventName == ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS) {
			if ($event->getLayout()->theme_plus_default_javascript_position == 'body') {
				$columns[] = 'position!=?';
				$values[]  = 'head';
			}
			else {
				$columns[] = 'position=?';
				$values[]  = 'body';
			}
		}
		else {
			if ($event->getLayout()->theme_plus_default_javascript_position == 'head') {
				$columns[] = 'position!=?';
				$values[]  = 'body';
			}
			else {
				$columns[] = 'position=?';
				$values[]  = 'head';
			}
		}

		$javascripts = JavaScriptModel::findBy(
			$columns,
			$values,
			['order' => 'sorting']
		);
		if ($javascripts) {
			foreach ($javascripts as $javaScript) {
				$asset = new DatabaseAsset($javaScript->row(), 'js');
				$event->append($asset, 50);
			}
		}
	}

	public function collectPageJavaScripts(CollectAssetsEvent $event, $eventName)
	{
		$page          = $event->getPage();
		$javaScriptIds = [];

		// add noinherit javascripts from current page
		if ($page->theme_plus_include_javascripts_noinherit) {
			$javaScriptIds = deserialize(
				$page->theme_plus_javascripts_noinherit,
				true
			);
		}

		// add inherited javascripts from page trail
		while ($page) {
			if ($page->theme_plus_include_javascripts) {
				$javaScriptIds = array_merge(
					$javaScriptIds,
					deserialize(
						$page->theme_plus_javascripts,
						true
					)
				);
			}

			$page = \PageModel::findByPk($page->pid);
		}

		if (empty($javaScriptIds)) {
			return;
		}

		$columns = ['(' . implode(' OR ', array_fill(0, count($javaScriptIds), 'id=?')) . ')'];
		$values  = $javaScriptIds;

		if ($eventName == ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS) {
			if ($event->getLayout()->theme_plus_default_javascript_position == 'body') {
				$columns[] = 'position!=?';
				$values[]  = 'head';
			}
			else {
				$columns[] = 'position=?';
				$values[]  = 'body';
			}
		}
		else {
			if ($event->getLayout()->theme_plus_default_javascript_position == 'head') {
				$columns[] = 'position!=?';
				$values[]  = 'body';
			}
			else {
				$columns[] = 'position=?';
				$values[]  = 'head';
			}
		}

		$javascripts = JavaScriptModel::findBy(
			$columns,
			$values,
			['order' => 'sorting']
		);
		if ($javascripts) {
			foreach ($javascripts as $javaScript) {
				$asset = new DatabaseAsset($javaScript->row(), 'js');
				$event->append($asset, 100);
			}
		}
	}
}
