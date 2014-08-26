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
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetOrganizerSubscriber implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::ORGANIZE_STYLESHEET_ASSETS => 'organizeAssets',
			ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS => 'organizeAssets',
		];
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
