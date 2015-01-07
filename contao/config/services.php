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
    function () {
        return new \Bit3\Contao\ThemePlus\RenderModeDeterminer(
            \Input::getInstance(),
            \Environment::getInstance()
        );
    }
);

$container['theme-plus-developer-tools'] = $container->share(
    function () {
        return new \Bit3\Contao\ThemePlus\DeveloperTool\DeveloperTool();
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
    function () {
        return new \Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler();
    }
);
