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

namespace ThemePlus;

use Template;
use FrontendTemplate;
use ThemePlus\Model\StylesheetModel;
use ThemePlus\Model\JavaScriptModel;
use ThemePlus\Model\VariableModel;
use ContaoAssetic\AsseticFactory;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;

/**
 * Class ThemePlus
 *
 * Adding files to the page layout.
 */
class BackendUserHack extends \BackendUser
{
	static public function destroyInstance()
	{
		\BackendUser::$objInstance = null;
	}
}
