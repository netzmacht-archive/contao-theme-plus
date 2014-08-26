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
use Symfony\Component\EventDispatcher\Event;

class GenerateAssetPathEvent extends Event
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
	 * @var AssetInterface
	 */
	protected $asset;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $path;

	public function __construct(\PageModel $page, \LayoutModel $layout, AssetInterface $asset, $type)
	{
		$this->page   = $page;
		$this->layout = $layout;
		$this->asset  = $asset;
		$this->type   = (string) $type;
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
	 * @return AssetInterface
	 */
	public function getAsset()
	{
		return $this->asset;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 *
	 * @return static
	 */
	public function setPath($path)
	{
		$this->path = (string) $path;
		return $this;
	}
}
