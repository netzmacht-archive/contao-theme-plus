<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package ThemePlus
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'InfinitySoft',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'InfinitySoft\ThemePlus\ThemePlus'                                   => 'system/modules/ThemePlus/ThemePlus.php',

	// Filter
	'InfinitySoft\ThemePlus\Filter\ContaoInsertTagFilter'                => 'system/modules/ThemePlus/Filter/ContaoInsertTagFilter.php',
	'InfinitySoft\ThemePlus\Filter\ContaoReplaceThemePlusVariableFilter' => 'system/modules/ThemePlus/Filter/ContaoReplaceThemePlusVariableFilter.php',
	'InfinitySoft\ThemePlus\Filter\ContaoReplaceVariableFilter'          => 'system/modules/ThemePlus/Filter/ContaoReplaceVariableFilter.php',

	// DataContainer
	'InfinitySoft\ThemePlus\DataContainer\JavaScript'                    => 'system/modules/ThemePlus/DataContainer/JavaScript.php',
	'InfinitySoft\ThemePlus\DataContainer\Layout'                        => 'system/modules/ThemePlus/DataContainer/Layout.php',
	'InfinitySoft\ThemePlus\DataContainer\Page'                          => 'system/modules/ThemePlus/DataContainer/Page.php',
	'InfinitySoft\ThemePlus\DataContainer\File'                          => 'system/modules/ThemePlus/DataContainer/File.php',
	'InfinitySoft\ThemePlus\DataContainer\Stylesheet'                    => 'system/modules/ThemePlus/DataContainer/Stylesheet.php',
	'InfinitySoft\ThemePlus\DataContainer\Collection'                    => 'system/modules/ThemePlus/DataContainer/Collection.php',
	'InfinitySoft\ThemePlus\DataContainer\Theme'                         => 'system/modules/ThemePlus/DataContainer/Theme.php',

	// Hybrid
	'InfinitySoft\ThemePlus\Hybrid\JavaScript'                           => 'system/modules/ThemePlus/Hybrid/JavaScript.php',

	// Model
	'InfinitySoft\ThemePlus\Model\JavaScriptModel'                       => 'system/modules/ThemePlus/Model/JavaScriptModel.php',
	'InfinitySoft\ThemePlus\Model\VariableModel'                         => 'system/modules/ThemePlus/Model/VariableModel.php',
	'InfinitySoft\ThemePlus\Model\StylesheetModel'                       => 'system/modules/ThemePlus/Model/StylesheetModel.php',
));
