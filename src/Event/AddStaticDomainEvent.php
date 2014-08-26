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

use Symfony\Component\EventDispatcher\Event;

class AddStaticDomainEvent extends Event
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
	 * @var string
	 */
	protected $url;

	public function __construct(\PageModel $page, \LayoutModel $layout, $url)
	{
		$this->page   = $page;
		$this->layout = $layout;
		$this->url    = (string) $url;
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
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 *
	 * @return static
	 */
	public function setUrl($url)
	{
		$this->url = (string) $url;
		return $this;
	}
}
