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

namespace Bit3\Contao\ThemePlus;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ConditionCompiler
{
    /**
     * @var ExpressionLanguage
     */
    private $language;

    /**
     * @var array
     */
    private $variables;

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

    public function compileHashKeyFunction($filterRules)
    {
        // TODO
    }

    public function compile($filterRules)
    {
        $expression = $this->parse($filterRules);

        return $this->language->compile($expression, array_keys($this->variables));
    }

    public function evaluate($filterRules)
    {
        $expression = $this->parse($filterRules);

        return $this->language->evaluate($expression, $this->variables);
    }

    public function parse($filterRules)
    {
        $or = [];

        foreach ($filterRules as $filterRule) {
            $and = [];

            $this->parsePlatformRule($filterRule, $and);
            $this->parseSystemRule($filterRule, $and);
            $this->parseBrowserRule($filterRule, $and);
            $this->parseBrowserVersionRule($filterRule, $and);

            $or[] = '(' . implode(' AND ', $and) . ')';
        }

        return '(' . implode(' OR ', $or) . ')';
    }

    private function parsePlatformRule($filterRule, &$and)
    {
        if ($filterRule['platform']) {
            switch ($filterRule['platform']) {
                case 'desktop':
                    $and[] = '!mobileDetect.isMobile()';
                    break;

                case 'tablet-or-mobile':
                    $and[] = 'mobileDetect.isMobile()';
                    break;

                case 'tablet':
                    $and[] = 'mobileDetect.isTablet()';
                    break;

                case 'mobile':
                    $and[] = 'mobileDetect.isMobile() AND NOT mobileDetect.isTablet()';
                    break;
            }
        }
    }

    private function parseSystemRule($filterRule, &$and)
    {
        if ($filterRule['system']) {
            $and[] = sprintf('mobileDetect.is(%s)', var_export($filterRule['system']));
        }
    }

    private function parseBrowserRule($filterRule, &$and)
    {
        if ($filterRule['browser']) {
            $and[] = sprintf('ikimeaBrowser.isBrowser(%s)', var_export($filterRule['browser']));
        }
    }

    private function parseBrowserVersionRule($filterRule, &$and)
    {
        if ($filterRule['browser'] && $filterRule['comparator'] && $filterRule['browser_version']) {
            switch ($filterRule['comparator']) {
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
                            $filterRule['comparator']
                        )
                    );
            }

            $and[] = sprintf(
                'version_compare(ikimeaBrowser.getVersion(), %s, %s)',
                var_export($filterRule['browser_version']),
                var_export($comparator)
            );
        }
    }
}
