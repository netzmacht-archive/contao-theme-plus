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

use Assetic\Asset\AssetInterface;

interface ExtendedAssetInterface extends AssetInterface
{
	/**
	 * @return string
	 */
	public function getConditionalComment();

	/**
	 * @param string $conditionalComment
	 *
	 * @return static
	 */
	public function setConditionalComment($conditionalComment);

	/**
	 * @return string
	 */
	public function getMediaQuery();

	/**
	 * @param string $mediaQuery
	 *
	 * @return static
	 */
	public function setMediaQuery($mediaQuery);

	/**
	 * @return ConditionInterface|null
	 */
	public function getCondition();

	/**
	 * @param ConditionInterface|null $condition
	 *
	 * @return static
	 */
	public function setCondition(ConditionInterface $condition = null);

	/**
	 * @return bool
	 */
	public function isInline();

	/**
	 * @param bool $inline
	 *
	 * @return static
	 */
	public function setInline($inline);

	/**
	 * @return bool
	 */
	public function isStandalone();

	/**
	 * @param bool $standalone
	 *
	 * @return static
	 */
	public function setStandalone($standalone);
}