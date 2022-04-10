<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace InstantWeb\Engine;

class Input
{
    private const REGEX_IP_FORMAT = '/^[a-z0-9:.]{7,}$/';
    private const DEFAULT_IP = '127.0.0.1';

    /**
     * Returns the IP address of the user.
     *
     * @return string The IP address.
     */
    public static function userIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? ''; // Use Null Coalescing opt if not defined.

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return preg_match(static::REGEX_IP_FORMAT, $ip) ? $ip : static::DEFAULT_IP;
    }

    /**
     * Returns the agent of the user.
     *
     * @return string The user agent
     */
    public static function userAgent(): string
    {
        return static::clean($_SERVER['HTTP_USER_AGENT']);
    }

    public static function post(string $key): string|bool
    {
        return isset($_POST[$key]) ? static::clean($_POST[$key]) : false;
    }

    public static function get(string $key): string|bool
    {
        return isset($_GET[$key]) ? static::clean($_GET[$key]) : false;
    }

    private static function clean(string $value): string
    {
        return strip_tags($value);
    }
}
