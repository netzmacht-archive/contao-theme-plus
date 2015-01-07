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

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * The compiler compiles or evaluate filter rules.
 */
class FilterRulesCompiler
{
    /**
     * The expression language.
     *
     * @var ExpressionLanguage
     */
    private $language;

    /**
     * The variables.
     *
     * @var array
     */
    private $variables;

    /**
     * Create a new compiler.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        $this->language = new ExpressionLanguage();
        $this->language->register(
            'version_compare',
            function ($str) {
                return 'version_compare(' . $str . ')';
            },
            function ($arguments, $str) {
                return call_user_func_array('version_compare', $arguments);
            }
        );

        $this->variables = [
            'mobileDetect'  => $GLOBALS['container']['mobile-detect'],
            'ikimeaBrowser' => $GLOBALS['container']['ikimea-browser'],
        ];
    }

    /**
     * Return the expression language.
     *
     * @return ExpressionLanguage
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Return the expression variables.
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Compile the filter rules.
     *
     * @param FilterRules $filterRules The filter rules.
     *
     * @return string
     */
    public function compile(FilterRules $filterRules)
    {
        $expression = $this->parse($filterRules);

        return $this->language->compile($expression, array_keys($this->variables));
    }

    /**
     * Evaluate the filter rules.
     *
     * @param FilterRules $filterRules The filter rules.
     *
     * @return string
     */
    public function evaluate(FilterRules $filterRules)
    {
        $expression = $this->parse($filterRules);

        return $this->language->evaluate($expression, $this->variables);
    }

    /**
     * Parse filter rules and return the expression.
     *
     * @param FilterRules $filterRules The filter rules.
     *
     * @return string
     */
    public function parse(FilterRules $filterRules)
    {
        $expressions = [];

        foreach ($filterRules as $filterRule) {
            $expressions[] = $this->parseRule($filterRule);
        }

        return empty($expressions) ? 'true' : implode(' or ', $expressions);
    }

    /**
     * Parse filter rule and return the expression.
     *
     * @param FilterRule $filterRule The filter rule.
     *
     * @return string
     */
    public function parseRule(FilterRule $filterRule)
    {
        $expressions = [];

        $this->parsePlatformRule($filterRule, $expressions);
        $this->parseSystemRule($filterRule, $expressions);
        $this->parseBrowserRule($filterRule, $expressions);
        $this->parseBrowserVersionRule($filterRule, $expressions);

        $expression = implode(' and ', $expressions);

        if ($filterRule->isInvert()) {
            $expression = 'not (' . $expression . ')';
        }

        return $expression;
    }

    /**
     * Parse the platform filter rule.
     *
     * @param FilterRule $filterRule  The filter rule.
     * @param array      $expressions The expression conjunction.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If the platform is not supported.
     */
    private function parsePlatformRule(FilterRule $filterRule, array &$expressions)
    {
        if (!$filterRule->getPlatform()) {
            return;
        }

        switch ($filterRule->getPlatform()) {
            case 'desktop':
                $expressions[] = '!mobileDetect.isMobile()';
                break;

            case 'mobile':
                $expressions[] = 'mobileDetect.isMobile()';
                break;

            case 'tablet':
                $expressions[] = 'mobileDetect.isTablet()';
                break;

            case 'phone':
                $expressions[] = 'mobileDetect.isMobile() and not mobileDetect.isTablet()';
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'Platform "%s" is not valid',
                        $filterRule->getPlatform()
                    )
                );
        }
    }

    /**
     * Parse the system filter rule.
     *
     * @param FilterRule $filterRule  The filter rule.
     * @param array      $expressions The expression conjunction.
     *
     * @return void
     */
    private function parseSystemRule(FilterRule $filterRule, array &$expressions)
    {
        if (!$filterRule->getSystem()) {
            return;
        }

        $expressions[] = sprintf('mobileDetect.is(%s)', var_export($filterRule->getSystem()));
    }

    /**
     * Parse the browser filter rule.
     *
     * @param FilterRule $filterRule  The filter rule.
     * @param array      $expressions The expression conjunction.
     *
     * @return void
     */
    private function parseBrowserRule(FilterRule $filterRule, array &$expressions)
    {
        if (!$filterRule->getBrowser()) {
            return;
        }

        $expressions[] = sprintf('ikimeaBrowser.isBrowser(%s)', var_export($filterRule->getBrowser()));
    }

    /**
     * Parse the browser version filter rule.
     *
     * @param FilterRule $filterRule  The filter rule.
     * @param array      $expressions The expression conjunction.
     *
     * @return void
     */
    private function parseBrowserVersionRule(FilterRule $filterRule, array &$expressions)
    {
        if (!$filterRule->getBrowser() || !$filterRule->getComparator() || !$filterRule->getVersion()) {
            return;
        }

        $comparator = $this->convertComparator($filterRule->getComparator());

        $expressions[] = sprintf(
            'version_compare(ikimeaBrowser.getVersion(), %s, %s)',
            var_export($filterRule->getVersion()),
            var_export($comparator)
        );
    }

    /**
     * Convert comparator into version_compare compatible comparator.
     *
     * @param string $comparator The comparator spec.
     *
     * @return string
     *
     * @throws \InvalidArgumentException If the comparator is not supported.
     */
    private function convertComparator($comparator)
    {
        switch ($comparator) {
            case '':
                $comparator = '==';
                break;
            case 'lt':
                $comparator = '<';
                break;
            case 'lte':
                $comparator = '<=';
                break;
            case 'ne':
                $comparator = '<>';
                break;
            case 'gte':
                $comparator = '>=';
                break;
            case 'gt':
                $comparator = '>';
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'The comparator "%s" is invalid',
                        $comparator
                    )
                );
        }

        return $comparator;
    }
}
