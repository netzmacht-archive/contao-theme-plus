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

namespace Bit3\Contao\ThemePlus;

use Template;
use FrontendTemplate;
use Bit3\Contao\ThemePlus\DataContainer\File;
use Bit3\Contao\ThemePlus\Model\StylesheetModel;
use Bit3\Contao\ThemePlus\Model\JavaScriptModel;
use Bit3\Contao\ThemePlus\Model\VariableModel;
use ContaoAssetic\AsseticFactory;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;

/**
 * Class ThemePlusEnvironment
 */
class ThemePlusEnvironment
{
	const BROWSER_IDENT_OVERWRITE = 'THEME_PLUS_BROWSER_IDENT_OVERWRITE';

	/**
	 * Singleton
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return ThemePlusEnvironment
	 */
	public static function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new ThemePlusEnvironment();

			if (TL_MODE == 'FE') {
				// request the BE_USER_AUTH login status
				$cookieName = 'BE_USER_AUTH';
				$ip   = \Environment::get('ip');
				$hash = \Input::cookie($cookieName);

				// Check the cookie hash
				if ($hash == sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $ip : '') . $cookieName))
				{
					$session = \Database::getInstance()
						->prepare("SELECT * FROM tl_session WHERE hash=? AND name=?")
						->execute($hash, $cookieName);

					// Try to find the session in the database
					if ($session->next())
					{
						$time = time();

						// Validate the session
						if ($session->sessionID == session_id() &&
							($GLOBALS['TL_CONFIG']['disableIpCheck'] || $session->ip == $ip) &&
							$session->hash == $hash &&
							($session->tstamp + $GLOBALS['TL_CONFIG']['sessionTimeout']) >= $time
						) {
							$userId = $session->pid;
							$user = \UserModel::findByPk($userId);

							if ($user) {
								static::setDesignerMode($user->themePlusDesignerMode || \Input::get('theme_plus_compile_assets'));
							}
						}
					}
				}
			}
		}
		return static::$instance;
	}

	/**
	 * @var \Ikimea\Browser\Browser
	 */
	protected static $browserDetect;

	/**
	 * @return \Ikimea\Browser\Browser
	 */
	public static function getBrowserDetect()
	{
		if (static::$browserDetect === null) {
			static::$browserDetect = new \Ikimea\Browser\Browser();
		}
		return static::$browserDetect;
	}

	/**
	 * @var \Mobile_Detect
	 */
	protected static $mobileDetect;

	/**
	 * @return \Mobile_Detect
	 */
	public static function getMobileDetect()
	{
		if (static::$mobileDetect === null) {
			static::$mobileDetect = new \Mobile_Detect();
		}
		return static::$mobileDetect;
	}

	/**
	 * If is in live mode.
	 */
	protected $blnLiveMode = true;

	/**
	 * Cached be login status.
	 */
	protected $blnBeLoginStatus = null;

	/**
	 * The variables cache.
	 */
	protected $arrVariables = null;

	/**
	 * List of all added files.
	 *
	 * @var array
	 */
	protected $files = array();

	/**
	 * Singleton constructor.
	 */
	protected function __construct()
	{
	}

	/**
	 * Get productive mode status.
	 */
	public static function isLiveMode()
	{
		return static::getInstance()->blnLiveMode
			? true
			: false;
	}


	/**
	 * Set productive mode.
	 */
	public static function setLiveMode($liveMode = true)
	{
		static::getInstance()->blnLiveMode = $liveMode;
	}


	/**
	 * Get productive mode status.
	 */
	public static function isDesignerMode()
	{
		return static::getInstance()->blnLiveMode
			? false
			: true;
	}


	/**
	 * Set designer mode.
	 */
	public static function setDesignerMode($designerMode = true)
	{
		static::getInstance()->blnLiveMode = !$designerMode;
	}


    /**
     * Determine if the pre-compile mode is enabled.
     *
     * @bool
     */
    public static function isInPreCompileMode()
    {
        return static::isDesignerMode() &&
            \Input::get('theme_plus_compile_assets');
    }

	/**
	 * Shorthand check if current request is from a desktop.
	 *
	 * @return bool
	 */
	public static function isDesktop()
	{
		$browserIdentOverwrite = json_decode(\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE));

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return $browserIdentOverwrite->platform == 'desktop';
		}

		return !(static::getMobileDetect()->isTablet() || static::getMobileDetect()->isMobile());
	}

	/**
	 * Shorthand check if current request is from a tablet.
	 *
	 * @return bool
	 */
	public static function isTabled()
	{
		trigger_error('Typo in method. Use isTablet instead.', E_USER_DEPRECATED);
		return static::isTablet();
	}

	/**
	 * Shorthand check if current request is from a tablet.
	 *
	 * @return bool
	 */
	public static function isTablet()
	{
		$browserIdentOverwrite = json_decode(\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE));

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return in_array($browserIdentOverwrite->platform, array('tablet', 'mobile'));
		}

		return static::getMobileDetect()->isTablet();
	}

	/**
	 * Shorthand check if current request is from a smartphone.
	 *
	 * @return bool
	 */
	public static function isSmartphone()
	{
		$browserIdentOverwrite = json_decode(\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE));

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return in_array($browserIdentOverwrite->platform, array('smartphone', 'mobile'));
		}

		return static::getMobileDetect()->isMobile() && !static::getMobileDetect()->isTablet();
	}

	/**
	 * Shorthand check if current request is from a mobile device.
	 *
	 * @return bool
	 */
	public static function isMobile()
	{
		$browserIdentOverwrite = json_decode(\Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE));

		if ($browserIdentOverwrite && $browserIdentOverwrite->platform) {
			return in_array($browserIdentOverwrite->platform, array('tablet', 'smartphone', 'mobile'));
		}

		return static::getMobileDetect()->isMobile() || static::getMobileDetect()->isTablet();
	}
}
