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

use Assetic\Asset\StringAsset;
use ContaoAssetic\AsseticFactory;
use Bit3\Contao\ThemePlus\Twig\CompileJsTokenParser;

class TwigExtension
{
	public function init(\ContaoTwig $twig)
	{
		$twig->getEnvironment()->addFilter(
			'compileJs',
			new \Twig_Filter_Function('Bit3\Contao\ThemePlus\TwigExtension::compileJs')
		);
		$twig->getEnvironment()->addFilter(
			'compileCss',
			new \Twig_Filter_Function('Bit3\Contao\ThemePlus\TwigExtension::compileCss')
		);
		$twig->getEnvironment()->addTokenParser(
			new CompileJsTokenParser()
		);
	}

	static public function compileJs($script, $filter = null, $debug = null)
	{
		if ($filter === null) {
			/** @var \PageModel $objPage */
			global $objPage;
			if (!$objPage->layout) {
				$objPage->loadDetails();
			}

			$layout = ThemePlusEnvironment::getPageLayout();
			if (!$layout) {
				return '';
			}
			$filter = $layout->asseticJavaScriptFilter;
		}
		if ($debug === null) {
			$debug = ThemePlus::isDesignerMode();
		}

		$defaultFilters = AsseticFactory::createFilterOrChain(
			$filter,
			$debug
		);

		$url = \Environment::get('request');

		$asset = new StringAsset($script, $defaultFilters, dirname($url), $url);
		return $asset->dump();
	}

	static public function compileCss($css, $filter = null, $debug = null)
	{
		if ($filter === null) {
			/** @var \PageModel $objPage */
			global $objPage;
			if (!$objPage->layout) {
				$objPage->loadDetails();
			}

			$layout = ThemePlusEnvironment::getPageLayout();
			if (!$layout) {
				return '';
			}
			$filter = $layout->asseticStylesheetFilter;
		}
		if ($debug === null) {
			$debug = ThemePlus::isDesignerMode();
		}

		$defaultFilters = AsseticFactory::createFilterOrChain(
			$filter,
			$debug
		);

		$url = \Environment::get('request');

		$asset = new StringAsset($css, $defaultFilters, dirname($url), $url);
		return $asset->dump();
	}
}
