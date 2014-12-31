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

namespace Bit3\Contao\ThemePlus\Filter;

/**
 * Class FilterRulesFactory
 */
class FilterRulesFactory
{
    /**
     * Rebuild the array from filter rules.
     *
     * @param FilterRules $filterRules The filter rules.
     *
     * @return array
     */
    public function createRulesArray(FilterRules $filterRules)
    {
        $filterRulesArray = [];

        foreach ($filterRules->all() as $filterRule) {
            $filterRulesArray[] = $this->createRuleArray($filterRule);
        }

        return $filterRules;
    }

    /**
     * Rebuild the array from a filter rule.
     *
     * @param FilterRule $filterRule The filter rule.
     *
     * @return array
     */
    public function createRuleArray(FilterRule $filterRule)
    {
        return [
            'platform'        => $filterRule->getPlatform(),
            'system'          => $filterRule->getSystem(),
            'browser'         => $filterRule->getBrowser(),
            'comparator'      => $filterRule->getComparator(),
            'browser_version' => $filterRule->getVersion(),
            'invert'          => $filterRule->isInvert(),
        ];
    }

    /**
     * Create new set of filter rules.
     *
     * @param array $filterRulesArray The filter rules array from the database.
     *
     * @return FilterRules
     */
    public function createRules(array $filterRulesArray)
    {
        $filterRules = new FilterRules();

        foreach ($filterRulesArray as $filterRuleArray) {
            $filterRule = $this->createRule($filterRuleArray);

            if ($filterRule) {
                $filterRules->add($filterRule);
            }
        }

        return $filterRules;
    }

    /**
     * Create a new filter rule.
     *
     * @param array $filterRuleArray the filter rule array from the database.
     *
     * @return FilterRule
     */
    public function createRule(array $filterRuleArray)
    {
        if (
            empty($filterRuleArray['platform'])
            && empty($filterRuleArray['system'])
            && empty($filterRuleArray['browser'])
            && empty($filterRuleArray['comparator'])
            && empty($filterRuleArray['browser_version'])
            && empty($filterRuleArray['invert'])
        ) {
            return null;
        }

        return new FilterRule(
            $filterRuleArray['platform'],
            $filterRuleArray['system'],
            $filterRuleArray['browser'],
            $filterRuleArray['comparator'],
            $filterRuleArray['browser_version'],
            $filterRuleArray['invert']
        );
    }
}
