<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
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
            defined('TL_ASSETS_URL')
            && strlen(TL_ASSETS_URL) > 0
            && strpos($url, TL_ASSETS_URL) === 0
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
            defined('TL_ASSETS_URL')
            && strlen(TL_ASSETS_URL) > 0
            && !preg_match('~^\w:~', $url)
        ) {
            $url = TL_ASSETS_URL . ltrim($url, '/');

            $event->setUrl($url);
        }
    }
}
