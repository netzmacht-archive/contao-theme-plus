<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Bit3\Contao\ThemePlus\Util;

/**
 * Class URIUtil.
 *
 * @package Bit3\Contao\ThemePlus\Util
 */
class UrlUtil
{
    /**
     * Check if source in an absolute url.
     *
     * @param string $source Source url.
     *
     * @return bool
     */
    public static function isAbsoluteUrl($source)
    {
        return preg_match('#^\w+:#', $source) || 0 === strpos($source, '//');
    }

    /**
     * Add scheme to the url.
     *
     * @param string $source Source url.
     * @param bool   $useSsl Use ssl.
     *
     * @return string
     */
    public static function addScheme($source, $useSsl = true)
    {
        if (strpos($source, '//') === 0) {
            $scheme = $useSsl ? 'https:' : 'http';
            $source = $scheme . $source;
        }

        return $source;
    }
}
