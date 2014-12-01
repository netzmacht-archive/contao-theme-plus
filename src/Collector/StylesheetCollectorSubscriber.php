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

namespace Bit3\Contao\ThemePlus\Collector;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssRewriteFilter;
use Bit3\Contao\ThemePlus\Asset\DatabaseAsset;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent;
use Bit3\Contao\ThemePlus\Event\StripStaticDomainEvent;
use Bit3\Contao\ThemePlus\Model\StylesheetModel;
use Bit3\Contao\ThemePlus\ThemePlusEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StylesheetCollectorSubscriber implements EventSubscriberInterface
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

    public function collectFrameworkStylesheets(
        CollectAssetsEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (is_array($GLOBALS['TL_FRAMEWORK_CSS']) && !empty($GLOBALS['TL_FRAMEWORK_CSS'])) {
            foreach (array_unique($GLOBALS['TL_FRAMEWORK_CSS']) as $stylesheet) {
                $stripStaticDomainEvent =
                    new StripStaticDomainEvent($event->getPage(), $event->getLayout(), $stylesheet);
                $eventDispatcher->dispatch(ThemePlusEvents::STRIP_STATIC_DOMAIN, $stripStaticDomainEvent);
                $stylesheet = $stripStaticDomainEvent->getUrl();

                $asset = new FileAsset(
                    TL_ROOT . DIRECTORY_SEPARATOR . $stylesheet,
                    [new CssRewriteFilter()],
                    TL_ROOT,
                    $stylesheet
                );

                $generateAssetPathEvent = new GenerateAssetPathEvent(
                    $event->getPage(),
                    $event->getLayout(),
                    $asset,
                    'css'
                );
                $eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

                $asset->setTargetPath($generateAssetPathEvent->getPath());
                $event->append($asset, -50);
            }

            $GLOBALS['TL_FRAMEWORK_CSS'] = [];
        }
    }

    public function collectRuntimeStylesheets(
        CollectAssetsEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (is_array($GLOBALS['TL_CSS']) && !empty($GLOBALS['TL_CSS'])) {
            foreach ($GLOBALS['TL_CSS'] as $stylesheet) {
                if ($stylesheet instanceof AssetInterface) {
                    $event->append($stylesheet);
                } else {
                    list($source, $media, $mode) = explode('|', $stylesheet);

                    $stripStaticDomainEvent =
                        new StripStaticDomainEvent($event->getPage(), $event->getLayout(), $source);
                    $eventDispatcher->dispatch(ThemePlusEvents::STRIP_STATIC_DOMAIN, $stripStaticDomainEvent);
                    $source = $stripStaticDomainEvent->getUrl();

                    $asset =
                        new ExtendedFileAsset(TL_ROOT . '/' . $source, [new CssRewriteFilter()], TL_ROOT, $stylesheet);
                    $asset->setMediaQuery($media);
                    $asset->setStandalone($mode != 'static');

                    $generateAssetPathEvent = new GenerateAssetPathEvent(
                        $event->getPage(),
                        $event->getLayout(),
                        $asset,
                        'css'
                    );
                    $eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

                    $asset->setTargetPath($generateAssetPathEvent->getPath());
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

    public function collectUserStylesheets(
        CollectAssetsEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (is_array($GLOBALS['TL_USER_CSS']) && !empty($GLOBALS['TL_USER_CSS'])) {
            foreach ($GLOBALS['TL_USER_CSS'] as $stylesheet) {
                if ($stylesheet instanceof AssetInterface) {
                    $event->append($stylesheet);
                } else {
                    list($source, $media, $mode, $version) = explode('|', $stylesheet);

                    $stripStaticDomainEvent =
                        new StripStaticDomainEvent($event->getPage(), $event->getLayout(), $source);
                    $eventDispatcher->dispatch(ThemePlusEvents::STRIP_STATIC_DOMAIN, $stripStaticDomainEvent);
                    $source = $stripStaticDomainEvent->getUrl();

                    $asset =
                        new ExtendedFileAsset(TL_ROOT . '/' . $source, [new CssRewriteFilter()], TL_ROOT, $stylesheet);
                    $asset->setMediaQuery($media);
                    $asset->setStandalone($mode != 'static');

                    $generateAssetPathEvent = new GenerateAssetPathEvent(
                        $event->getPage(),
                        $event->getLayout(),
                        $asset,
                        'css'
                    );
                    $eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

                    $asset->setTargetPath($generateAssetPathEvent->getPath());
                    $event->append($asset, 150);
                }
            }

            $GLOBALS['TL_USER_CSS'] = [];
        }
    }
}
