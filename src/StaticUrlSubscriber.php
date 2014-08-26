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

use Bit3\Contao\ThemePlus\Event\AddStaticDomainEvent;
use Bit3\Contao\ThemePlus\Event\StripStaticDomainEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StaticUrlSubscriber implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			ThemePlusEvents::STRIP_STATIC_DOMAIN => 'stripStaticDomain',
			ThemePlusEvents::ADD_STATIC_DOMAIN   => 'addStaticDomain',
		];
	}

	public function stripStaticDomain(StripStaticDomainEvent $event)
	{
		$url = $event->getUrl();

		if (
			defined('TL_ASSETS_URL') &&
			strlen(TL_ASSETS_URL) > 0 &&
			strpos($url, TL_ASSETS_URL) === 0
		) {
			$url = substr(
				$url,
				strlen(TL_ASSETS_URL)
			);

			$event->setUrl($url);
		}
	}

	public function addStaticDomain(AddStaticDomainEvent $event)
	{
		$url = $event->getUrl();

		if (
			defined('TL_ASSETS_URL') &&
			strlen(TL_ASSETS_URL) > 0 &&
			!preg_match('~^\w:~', $url)
		) {
			$url = TL_ASSETS_URL . ltrim($url, '/');

			$event->setUrl($url);
		}
	}
}
