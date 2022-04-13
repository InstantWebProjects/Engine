<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace InstantWeb\Engine;

class User
{
    public const USER_ID_SESSION_NAME = 'userId';
    public const EMAIL_SESSION_NAME = 'email';

    private const HASH_LENGTH = 4; // When get more user, will need to increase it!

    public static function setAuth(int $userId, string $email): void
    {
        Session::sets(
            [
                static::USER_ID_SESSION_NAME => $userId,
                static::EMAIL_SESSION_NAME => $email
            ]
        );
    }

    public static function isLoggedIn(): bool
    {
        return (bool)(Session::exist(static::USER_ID_SESSION_NAME));
    }

    public static function getId(): int
    {
        return (int)Session::get(static::USER_ID_SESSION_NAME);
    }

    public static function getEmail(): string
    {
        return Session::get(static::EMAIL_SESSION_NAME);
    }

    /**
     * @return string Returns a unique ID string.
     */
    public static function generateHash(): string
    {
        $prefix = (string)mt_rand();

        return substr(md5(uniqid($prefix, true)), 0, static::HASH_LENGTH);
    }
}
