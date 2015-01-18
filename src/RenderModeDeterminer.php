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

/**
 * Class RenderModeDeterminer
 */
class RenderModeDeterminer
{
    const COOKIE_NAME = 'BE_USER_AUTH';

    /**
     * @var \Input
     */
    private $input;

    /**
     * @var \Environment
     */
    private $environment;

    /**
     * @var \Database
     */
    private $database;

    public function __construct(\Input $input, \Environment $environment, \Database $database)
    {
        $this->input       = $input;
        $this->environment = $environment;
        $this->database = $database;
    }

    /**
     * Determine the current rendering mode.
     *
     * @return string
     */
    public function determineMode()
    {
        if (TL_MODE == 'FE') {
            $user = $this->resolveBackendUser();

            if ($user) {
                if ($this->input->get('theme_plus_compile_assets')) {
                    return RenderMode::PRE_COMPILE;
                } elseif ($user->themePlusDesignerMode) {
                    return RenderMode::DESIGN;
                }
            }

            return RenderMode::LIVE;
        }

        throw new \RuntimeException('Render mode can only determined in FE mode');
    }

    /**
     * Resolve the user from the session.
     *
     * @return \UserModel
     *
     * @internal
     */
    public function resolveBackendUser()
    {
        if (TL_MODE == 'FE') {
            // request the BE_USER_AUTH login status
            $hash = $this->input->cookie(self::COOKIE_NAME);

            // Check the cookie hash
            if ($this->validateHash($hash)) {
                $session = $this->database
                    ->prepare("SELECT * FROM tl_session WHERE hash=? AND name=?")
                    ->execute($hash, self::COOKIE_NAME);

                // Try to find the session in the database
                if ($session->next() && $this->validateUserSession($hash, $session)) {
                    $userId = $session->pid;
                    $user   = \UserModel::findByPk($userId);

                    return $user;
                }
            }
        }

        return null;
    }

    /**
     * Validate the cookie hash.
     *
     * @param string $hash
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function validateHash($hash)
    {
        $ipAddress = $this->environment->get('ip');

        return $hash == sha1(
            session_id() . ($GLOBALS['TL_CONFIG']['disableIpCheck'] ? '' : $ipAddress) . self::COOKIE_NAME
        );
    }

    /**
     * Validate the user session record.
     *
     * @param \Database_Result $session
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function validateUserSession($hash, \Database_Result $session)
    {
        $ipAddress = $this->environment->get('ip');
        $time      = time();

        if (
            $session->sessionID == session_id()
            && (
                $GLOBALS['TL_CONFIG']['disableIpCheck']
                || $session->ip == $ipAddress
            )
            && $session->hash == $hash
            && ($session->tstamp + $GLOBALS['TL_CONFIG']['sessionTimeout']) >= $time
        ) {
            return true;
        }

        return false;
    }
}
