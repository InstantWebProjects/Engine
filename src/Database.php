<?php
/**
 * (c) Pierre-Henry Soria <hi@ph7.me> - All Rights Reserved.
 */

declare(strict_types=1);

namespace InstantApp\Engine;

use PDO;
use PDOException;
use PDOStatement;

final class Database
{
    private static ?PDO $pdo = null;

    private static ?PDOStatement$stmt = null;

    public static function connect(array $dbDetails = []): void
    {
        try {
            static::$pdo = new PDO(
                'mysql:host=' . $dbDetails['dbHost'] . ';dbname=' . $dbDetails['dbName'],
                $dbDetails['dbUser'],
                $dbDetails['dbPass']
            );
            static::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            exit('<h1>An unexpected database error occured.</h1>');
        }
    }

    /**
     * Prepares a query and executes if applicable.
     *
     * @param string $sql The SQL to prepare
     * @param array $binds Values to bind to the query
     * @param bool $execute Automatically execute?
     *
     * @return void
     */
    public static function query(string $sql, array $binds = [], bool $execute = true): void
    {
        static::$stmt = static::$pdo->prepare($sql);

        foreach ($binds as $key => $value) {
            static::$stmt->bindValue($key, $value);
        }

        if ($execute === true) {
            static::$stmt->execute();
        }
    }

    /**
     * Returns the number of affected rows from the last executed query.
     *
     * @return int The number of affected rows
     */
    public static function rowCount(): int
    {
        return static::$stmt->rowCount();
    }

    /**
     * Returns a single row.
     *
     * @return mixed The row as an object.
     */
    public static function fetch()
    {
        return static::$stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Returns all rows
     *
     * @return array|null The rows as an object.
     */
    public static function fetchAll(): ?array
    {
        return static::$stmt->fetchAll(PDO::FETCH_OBJ) ?? null;
    }

    public static function quote(string $string): string
    {
        return static::$pdo->quote($string);
    }

    public static function lastInsertId(): string
    {
        return (string)static::$pdo->lastInsertId();
    }

    /**
     * It's a static class, to prevent instantiation of the class
     */
    private function __construct() {}
    private function __clone() {}
}
