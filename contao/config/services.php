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


/** @var Pimple $container */

$container['theme-plus-render-mode-determiner'] = $container->share(
    function ($container) {
        $input       = $container['input'];
        $environment = $container['environment'];
        $database = $container['database.connection'];

        return new \Bit3\Contao\ThemePlus\RenderModeDeterminer($input, $environment, $database);
    }
);

$container['theme-plus-developer-tools'] = $container->share(
    function ($container) {
        return new \Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool($container['page-provider']);
    }
);

$container['theme-plus-assets-cache'] = $container->share(
    function () {
        return new \Doctrine\Common\Cache\FilesystemCache(
            implode(DIRECTORY_SEPARATOR, [TL_ROOT, 'system', 'cache', 'assets'])
        );
    }
);

$container['theme-plus-filter-rules-factory'] = $container->share(
    function () {
        return new \Bit3\Contao\ThemePlus\Filter\FilterRulesFactory();
    }
);

$container['theme-plus-filter-rules-compiler'] = $container->share(
    function ($container) {
        $mobileDetect  = $container['mobile-detect'];
        $ikimeaBrowser = $container['ikimea-browser'];
        return new \Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler($mobileDetect, $ikimeaBrowser);
    }
);

$container['theme-plus-cache-generator-subscriber'] = $container->share(
    function ($container) {
        $compiler      = $container['theme-plus-filter-rules-compiler'];
        $developerTool = $container['theme-plus-developer-tools'];

        return new \Bit3\Contao\ThemePlus\Cache\CacheGeneratorSubscriber($compiler, $developerTool);
    }
);

$container['theme-plus-stylesheet-collector-subscriber'] = $container->share(
    function ($container) {
        $asseticFactory     = $container['assetic.factory'];
        $filterRulesFactory = $container['theme-plus-filter-rules-factory'];

        return new \Bit3\Contao\ThemePlus\Collector\StylesheetCollectorSubscriber($asseticFactory, $filterRulesFactory);
    }
);

$container['theme-plus-javascript-collector-subscriber'] = $container->share(
    function ($container) {
        $asseticFactory     = $container['assetic.factory'];
        $filterRulesFactory = $container['theme-plus-filter-rules-factory'];

        return new \Bit3\Contao\ThemePlus\Collector\JavaScriptCollectorSubscriber($asseticFactory, $filterRulesFactory);
    }
);

$container['theme-plus-stylesheet-renderer-subscriber'] = $container->share(
    function ($container) {
        $pageProvider  = $container['page-provider'];
        $developerTool = $container['theme-plus-developer-tools'];

        return new \Bit3\Contao\ThemePlus\Renderer\StylesheetRendererSubscriber($pageProvider, $developerTool);
    }
);

$container['theme-plus-javascript-renderer-subscriber'] = $container->share(
    function ($container) {
        $pageProvider  = $container['page-provider'];
        $developerTool = $container['theme-plus-developer-tools'];

        return new \Bit3\Contao\ThemePlus\Renderer\JavaScriptRendererSubscriber($pageProvider, $developerTool);
    }
);

$container['theme-plus-asset-organizer-subscriber'] = $container->share(
    function ($container) {
        $compiler = $container['theme-plus-filter-rules-compiler'];

        return new \Bit3\Contao\ThemePlus\Organizer\AssetOrganizerSubscriber($compiler);
    }
);

$container['theme-plus-static-url-subscriber'] = $container->share(
    function () {
        return new \Bit3\Contao\ThemePlus\StaticUrlSubscriber();
    }
);

$container['theme-plus-backend-integration'] = $container->share(
    function ($container) {
        $cache = $container['theme-plus-assets-cache'];

        return new \Bit3\Contao\ThemePlus\BackendIntegration($cache);
    }
);

$container['theme-plus-generic-handler'] = $container->share(
    function ($container) {
        $pageProvider         = $container['page-provider'];
        $renderModeDeterminer = $container['theme-plus-render-mode-determiner'];
        $cache                = $container['theme-plus-assets-cache'];
        $compiler             = $container['theme-plus-filter-rules-compiler'];
        $developerTool        = $container['theme-plus-developer-tools'];

        return new \Bit3\Contao\ThemePlus\ThemePlus(
            $pageProvider,
            $renderModeDeterminer,
            $cache,
            $compiler,
            $developerTool
        );
    }
);

$container['theme-plus-stylesheet-handler'] = $container->share(
    function ($container) {
        $pageProvider         = $container['page-provider'];
        $eventDispatcher      = $container['event-dispatcher'];
        $asseticFactory       = $container['assetic.factory'];
        $renderModeDeterminer = $container['theme-plus-render-mode-determiner'];
        $cache                = $container['theme-plus-assets-cache'];
        $compiler             = $container['theme-plus-filter-rules-compiler'];

        return new \Bit3\Contao\ThemePlus\StylesheetsHandler(
            $pageProvider,
            $eventDispatcher,
            $asseticFactory,
            $renderModeDeterminer,
            $cache,
            $compiler
        );
    }
);

$container['theme-plus-javascript-handler'] = $container->share(
    function ($container) {
        $pageProvider         = $container['page-provider'];
        $eventDispatcher      = $container['event-dispatcher'];
        $asseticFactory       = $container['assetic.factory'];
        $renderModeDeterminer = $container['theme-plus-render-mode-determiner'];
        $cache                = $container['theme-plus-assets-cache'];
        $compiler             = $container['theme-plus-filter-rules-compiler'];

        return new \Bit3\Contao\ThemePlus\JavaScriptsHandler(
            $pageProvider,
            $eventDispatcher,
            $asseticFactory,
            $renderModeDeterminer,
            $cache,
            $compiler
        );
    }
);

$container['theme-plus-maintenance-build-asset-cache'] = $container->share(
    function ($container) {
        $session = $container['session'];
        $cache   = $container['theme-plus-assets-cache'];

        return new \Bit3\Contao\ThemePlus\Maintenance\BuildAssetCache($session, $cache);
    }
);
