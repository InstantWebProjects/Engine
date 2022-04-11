<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace InstantWeb\Engine;

class Session
{
    /**
     * Sets a session.
     *
     * @param string $key The key
     * @param string|int $value The value
     *
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $sessionName)
    {
        return $_SESSION[$sessionName] ?? false;
    }

    public static function exist(string $sessionName): bool
    {
        return !empty($_SESSION[$sessionName]);
    }

    /**
     * Destroys the session.
     *
     * @return void
     */
    public static function destroy(): void
    {
        if (!empty($_SESSION)) {
            $_SESSION = [];
            session_unset();
            session_destroy();
        }
    }

    public static function isActivated(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
