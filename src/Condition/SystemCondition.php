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

namespace Bit3\Contao\ThemePlus\Condition;

class SystemCondition implements ConditionInterface
{

    private $system;

    public function __construct($system)
    {
        $this->system = $system;
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        /* @var \Pimple $container */
        global $container;

        /** @var \Mobile_Detect $mobileDetect */
        $mobileDetect = $container['mobile-detect'];

        return $mobileDetect->is($this->system);
    }
}