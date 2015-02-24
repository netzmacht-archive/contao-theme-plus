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
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

namespace Bit3\Contao\ThemePlus\Collector;

use Assetic\Asset\AssetInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedFileAsset;
use Bit3\Contao\ThemePlus\Asset\ExtendedHttpAsset;
use Bit3\Contao\ThemePlus\Event\CollectAssetsEvent;
use Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent;
use Bit3\Contao\ThemePlus\Event\StripStaticDomainEvent;
use Bit3\Contao\ThemePlus\Model\JavaScriptModel;
use Bit3\Contao\ThemePlus\ThemePlusEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JavaScriptCollectorSubscriber extends AbstractAssetCollector implements EventSubscriberInterface
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

    public function collectRuntimeJavaScripts(
        CollectAssetsEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        if (
            $eventName == ThemePlusEvents::COLLECT_HEAD_JAVASCRIPT_ASSETS
            && $event->getLayout()->theme_plus_default_javascript_position != 'head'
            || $eventName == ThemePlusEvents::COLLECT_BODY_JAVASCRIPT_ASSETS
            && $event->getLayout()->theme_plus_default_javascript_position != 'body'
        ) {
            return;
        }

        if (is_array($GLOBALS['TL_JAVASCRIPT']) && !empty($GLOBALS['TL_JAVASCRIPT'])) {
            foreach ($GLOBALS['TL_JAVASCRIPT'] as $javaScript) {
                if ($javaScript instanceof AssetInterface) {
                    $event->append($javaScript);
                } else {
                    list($javaScript, $mode) = explode('|', $javaScript);

                    $stripStaticDomainEvent = new StripStaticDomainEvent(
                        $event->getRenderMode(),
                        $event->getPage(),
                        $event->getLayout(),
                        $javaScript
                    );
                    $eventDispatcher->dispatch(ThemePlusEvents::STRIP_STATIC_DOMAIN, $stripStaticDomainEvent);
                    $javaScript = $stripStaticDomainEvent->getUrl();

                    if ($this->isLocalAssets($javaScript)) {
                        $asset = new ExtendedFileAsset(TL_ROOT . '/' . $javaScript, [], TL_ROOT, $javaScript);
                    } else {
                        $asset = new ExtendedHttpAsset($javaScript);
                    }

                    $asset->setStandalone($mode != 'static');

                    $generateAssetPathEvent = new GenerateAssetPathEvent(
                        $event->getRenderMode(),
                        $event->getPage(),
                        $event->getLayout(),
                        $asset,
                        $event->getDefaultFilters(),
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
            } else {
                $columns[] = 'position=?';
                $values[]  = 'body';
            }
        } else {
            if ($event->getLayout()->theme_plus_default_javascript_position == 'head') {
                $columns[] = 'position!=?';
                $values[]  = 'body';
            } else {
                $columns[] = 'position=?';
                $values[]  = 'head';
            }
        }

        $collection = JavaScriptModel::findBy(
            $columns,
            $values,
            ['order' => 'sorting']
        );
        if ($collection) {
            $this->appendDatabaseAssets($event, $collection, 'js');
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
            } else {
                $columns[] = 'position=?';
                $values[]  = 'body';
            }
        } else {
            if ($event->getLayout()->theme_plus_default_javascript_position == 'head') {
                $columns[] = 'position!=?';
                $values[]  = 'body';
            } else {
                $columns[] = 'position=?';
                $values[]  = 'head';
            }
        }

        $collection = JavaScriptModel::findBy(
            $columns,
            $values,
            ['order' => 'sorting']
        );
        if ($collection) {
            $this->appendDatabaseAssets($event, $collection, 'js');
        }
    }
}
