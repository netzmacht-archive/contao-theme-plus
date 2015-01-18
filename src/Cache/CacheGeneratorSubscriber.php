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

namespace Bit3\Contao\ThemePlus\Cache;

use Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool;
use Bit3\Contao\ThemePlus\Event\GeneratePreCompiledAssetsCacheEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Bit3\Contao\ThemePlus\Event\RenderAssetHtmlEvent;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use Bit3\Contao\ThemePlus\RenderMode;
use Bit3\Contao\ThemePlus\ThemePlusEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CacheGeneratorSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilterRulesCompiler
     */
    private $compiler;

    /**
     * @var DeveloperTool
     */
    private $developerTool;

    public function __construct(FilterRulesCompiler $compiler, DeveloperTool $developerTool)
    {
        $this->compiler      = $compiler;
        $this->developerTool = $developerTool;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ThemePlusEvents::GENERATE_PRE_COMPILED_STYLESHEET_ASSETS_CACHE => 'generateStylesheetCache',
            ThemePlusEvents::GENERATE_PRE_COMPILED_JAVASCRIPT_ASSETS_CACHE => 'generateJavaScriptCache',
        ];
    }

    public function generateStylesheetCache(
        GeneratePreCompiledAssetsCacheEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->generateCache(
            $event,
            $eventDispatcher,
            ThemePlusEvents::ORGANIZE_STYLESHEET_ASSETS,
            ThemePlusEvents::RENDER_STYLESHEET_HTML
        );
    }

    public function generateJavaScriptCache(
        GeneratePreCompiledAssetsCacheEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->generateCache(
            $event,
            $eventDispatcher,
            ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS,
            ThemePlusEvents::RENDER_JAVASCRIPT_HTML
        );
    }

    private function generateCache(
        GeneratePreCompiledAssetsCacheEvent $event,
        EventDispatcherInterface $eventDispatcher,
        $organizeEventName,
        $renderEventName
    ) {
        if ($event->getCacheCode()) {
            return;
        }

        $date           = date('r');
        $page           = $event->getPage();
        $layout         = $event->getLayout();
        $defaultFilters = $event->getDefaultFilters();
        $collections    = $event->getCollections();

        $result = <<<PHP

/**
 * Assets cache
 *
 * created at: {$date}
 * page id: {$page->id}
 * layout id: {$layout->id}
 */

PHP;

        // store all collections
        foreach ($collections as $collection) {
            $organizeEvent = new OrganizeAssetsEvent(
                RenderMode::LIVE,
                $page,
                $layout,
                $collection,
                $defaultFilters
            );
            $eventDispatcher->dispatch($organizeEventName, $organizeEvent);

            $assets = $organizeEvent->getOrganizedAssets()->all();
            $debug  = '';
            $html   = '';

            foreach ($assets as $asset) {
                $renderEvent = new RenderAssetHtmlEvent(RenderMode::LIVE, $page, $layout, $asset, $defaultFilters);
                $eventDispatcher->dispatch($renderEventName, $renderEvent);

                $debug .= $this->developerTool->getAssetDebugString($asset) . PHP_EOL . PHP_EOL;
                $html .= $renderEvent->getHtml();
            }

            if ($html) {
                $expression = $this->compiler->compile($collection->getFilterRules());

                $debug = rtrim($debug);
                $debug = explode(PHP_EOL, $debug);
                $debug = implode(PHP_EOL . '     * ', $debug);
                $debug = <<<PHP
    /*
     * Debug information:
     *
     * {$debug}
     */
PHP;


                $html = var_export($html, true);

                $result .= <<<PHP

if ({$expression}) {
{$debug}
    return {$html};
}

PHP;
            }
        }

        $result .= <<<PHP

return null;

PHP;

        $event->setCacheCode($result);
    }
}
