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

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Bit3\Contao\ThemePlus\DeveloperTool;
use Symfony\Component\EventDispatcher\Event;

class OrganizeAssetsEvent extends Event
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
	 * @var AssetCollectionInterface
	 */
	protected $assets;

	/**
	 * @var AssetCollectionInterface|null
	 */
	protected $organizedAssets;

	public function __construct(
		\PageModel $page,
		\LayoutModel $layout,
		$defaultFilters,
		AssetCollectionInterface $assets,
		DeveloperTool $developerTool = null
	) {
		$this->page           = $page;
		$this->layout         = $layout;
		$this->defaultFilters = $defaultFilters;
		$this->assets         = $assets;
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
	 * @return AssetCollectionInterface
	 */
	public function getAssets()
	{
		return $this->assets;
	}

	/**
	 * @return DeveloperTool|null
	 */
	public function getDeveloperTool()
	{
		return $this->developerTool;
	}

	/**
	 * @return AssetCollectionInterface|null
	 */
	public function getOrganizedAssets()
	{
		return $this->organizedAssets;
	}

	/**
	 * @param AssetCollectionInterface|null $organizedAssets
	 *
	 * @return static
	 */
	public function setOrganizedAssets(AssetCollectionInterface $organizedAssets = null)
	{
		$this->organizedAssets = $organizedAssets;
		return $this;
	}
}
