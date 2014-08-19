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

namespace Bit3\Contao\ThemePlus\Event;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Bit3\Contao\ThemePlus\DeveloperTool;
use Symfony\Component\EventDispatcher\Event;

class RenderAssetHtmlEvent extends Event
{

	/**
	 * @var \PageModel
	 */
	protected $page;

	/**
	 * @var \LayoutModel
	 */
	protected $layout;

	/**
	 * @var FilterInterface[]|null
	 */
	protected $defaultFilters = [];

	/**
	 * @var AssetInterface
	 */
	protected $asset;

	/**
	 * @var DeveloperTool|null
	 */
	protected $developerTool;

	/**
	 * @var string
	 */
	protected $html;

	public function __construct(
		\PageModel $page,
		\LayoutModel $layout,
		$defaultFilters,
		$asset,
		DeveloperTool $developerTool = null
	) {
		$this->page           = $page;
		$this->layout         = $layout;
		$this->defaultFilters = $defaultFilters;
		$this->asset          = $asset;
		$this->developerTool  = $developerTool;
	}

	/**
	 * @return \PageModel
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @return \LayoutModel
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * @return \Assetic\Filter\FilterInterface[]|null
	 */
	public function getDefaultFilters()
	{
		return $this->defaultFilters;
	}

	/**
	 * @return AssetInterface
	 */
	public function getAsset()
	{
		return $this->asset;
	}

	/**
	 * @return DeveloperTool|null
	 */
	public function getDeveloperTool()
	{
		return $this->developerTool;
	}

	/**
	 * @return string
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * @param string $html
	 *
	 * @return static
	 */
	public function setHtml($html)
	{
		$this->html = (string) $html;
		return $this;
	}
}
