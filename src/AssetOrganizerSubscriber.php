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
use Assetic\Asset\StringAsset;
use Bit3\Contao\ThemePlus\Asset\DelegatorAssetInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetOrganizerSubscriber implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::GENERATE_ASSET_PATH        => 'generateAssetPath',
			ThemePlusEvents::ORGANIZE_STYLESHEET_ASSETS => 'organizeAssets',
			ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS => 'organizeAssets',
		];
	}

	public function generateAssetPath(
		GenerateAssetPathEvent $event,
		$eventName,
		EventDispatcherInterface $eventDispatcher
	) {
		$asset = $event->getAsset();

		$filters = [];
		foreach ($asset->getFilters() as $v) {
			$filters[] = get_class($v);
		}
		$filters = '[' . implode(
				',',
				$filters
			) . ']';

		while ($asset instanceof DelegatorAssetInterface) {
			$asset = $asset->getAsset();
		}

		// calculate path for collections
		if ($asset instanceof AssetCollectionInterface) {
			$string = $filters;

			foreach ($asset->all() as $child) {
				$generateAssetPathEvent = new GenerateAssetPathEvent(
					$event->getPage(),
					$event->getLayout(),
					$child,
					$event->getType()
				);
				$eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

				$string .= '-' . $generateAssetPathEvent->getPath();
			}

			$assetPath = sprintf(
				'assets/%s/%s-collection.%s',
				$event->getType(),
				substr(
					md5($string),
					0,
					8
				),
				$event->getType()
			);
		}

		// calculate cache path from content
		else if ($asset instanceof StringAsset) {
			$assetPath = sprintf(
				'assets/%s/%s-%s.%s',
				$event->getType(),
				substr(
					md5($filters . '-' . $asset->getContent() . '-' . $asset->getLastModified()),
					0,
					8
				),
				basename($asset->getSourcePath()),
				$event->getType()
			);
		}

		// calculate cache path from source path
		else {
			$assetPath = sprintf(
				'assets/%s/%s-%s.%s',
				$event->getType(),
				substr(
					md5($filters . '-' . $asset->getSourcePath() . '-' . $asset->getLastModified()),
					0,
					8
				),
				basename(
					$asset->getSourcePath(),
					'.' . $event->getType()
				),
				$event->getType()
			);
		}

		$event->setPath($assetPath);
	}

	public function organizeAssets(OrganizeAssetsEvent $event)
	{
		if (!$event->getOrganizedAssets()) {
			$combinedAssets = new AssetCollection();

			$organizedAssets = new AssetCollection();
			$organizedAssets->add($combinedAssets);

			$this->splitAssets($event->getAssets(), $organizedAssets, $combinedAssets, $event);

			$event->setOrganizedAssets($organizedAssets);
		}
	}

	protected function splitAssets(
		AssetCollectionInterface $assets,
		AssetCollectionInterface $organizedAssets,
		AssetCollectionInterface $combinedAssets,
		OrganizeAssetsEvent $event
	) {
		foreach ($assets as $asset) {
			if ($event->getDeveloperTool() && $asset instanceof AssetCollectionInterface) {
				static::splitAssets($asset, $organizedAssets, $combinedAssets, $event);
			}
			else if (
				$event->getDeveloperTool() ||
				$asset instanceof ExtendedAssetInterface && (
					$asset->getMediaQuery() ||
					$asset->getConditionalComment() ||
					$asset->isInline() ||
					$asset->isStandalone()
				)
			) {
				$organizedAssets->add($asset);
			}
			else {
				$combinedAssets->add($asset);
			}
		}
	}
}
