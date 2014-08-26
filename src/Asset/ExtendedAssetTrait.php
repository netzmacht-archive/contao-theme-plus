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

namespace Bit3\Contao\ThemePlus\Asset;

trait ExtendedAssetTrait
{

	/**
	 * @var string
	 */
	protected $conditionalComment;

	/**
	 * @var string
	 */
	protected $mediaQuery;

	/**
	 * @var ConditionInterface|null
	 */
	protected $condition;

	/**
	 * @var bool
	 */
	protected $inline = false;

	/**
	 * @var bool
	 */
	protected $standalone = false;

	/**
	 * {@inheritdoc}
	 */
	public function getConditionalComment()
	{
		return $this->conditionalComment;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setConditionalComment($conditionalComment)
	{
		$this->conditionalComment = (string) $conditionalComment;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMediaQuery()
	{
		return $this->mediaQuery;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setMediaQuery($mediaQuery)
	{
		$this->mediaQuery = (string) $mediaQuery;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCondition()
	{
		return $this->condition;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setCondition(ConditionInterface $condition = null)
	{
		$this->condition = $condition;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInline()
	{
		return $this->inline;
	}

	/**
	 * @param bool $inline
	 *
	 * @return static
	 */
	public function setInline($inline)
	{
		$this->inline = (bool) $inline;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isStandalone()
	{
		return $this->standalone;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setStandalone($standalone)
	{
		$this->standalone = (bool) $standalone;
		return $this;
	}
}