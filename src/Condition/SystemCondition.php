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

namespace Bit3\Contao\ThemePlus\Condition;

use Bit3\Contao\ThemePlus\ThemePlusEnvironment;

class SystemCondition
   implements ConditionInterface
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
