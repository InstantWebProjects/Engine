<?php
/**
 * (c) Pierre-Henry Soria <hi@ph7.me> - All Rights Reserved.
 */

declare(strict_types=1);

namespace InstantApp\Engine;

use Exception;
use http\Exception\RuntimeException;
use InstantWeb\Engine\Database;
use InstantWeb\Engine\Main;
use InstantWeb\Engine\Session;

require_once 'vendor/autoload.php';
require 'config/environment.php';
require 'helpers/misc.php';

class Bootstrap
{
    private const DEFAULT_TIMEZONE = 'America/Chicago';

    public function __construct(array $dbDetails)
    {
        $this->autoloader();

        try {
            Database::connect($dbDetails);
        } catch (Exception $except) {
            echo $except->getMessage();
        }
    }

    public function run(string $routesFile): void
    {
        $fullPath = $routesFile . '.php';

        if (is_file($fullPath)) {
            require $fullPath;
        } else {
            throw new RuntimeException(
                sprintf('Cannot find "%s"' . $fullPath)
            );
        }
    }

    public function initializeDebugging(): void
    {
        // First, convert "true/false" string from phpdotenv to boolean
        $debugMode = filter_var($_ENV['DEV_MODE'], FILTER_VALIDATE_BOOLEAN);

        if ($debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        }
    }

    /**
     * Check if the session is already initialized and initialize it if it isn't the case.
     *
     * @return void
     */
    public function initializePHPSession(): void
    {
        if (!Session::isActivated()) {
            @session_start();
        }
    }

    /**
     * Set a default timezone if it is not already configured in environment.
     *
     * @return void
     */
    public function setTimezoneIfNotSet(): void
    {
        if (!ini_get('date.timezone')) {
            ini_set('date.timezone', self::DEFAULT_TIMEZONE);
        }
    }

    private function autoloader(): void
    {
        spl_autoload_register(function (string $className) {
            $className = str_replace([__NAMESPACE__ . '\\', '\\'], '/', $className);
            $filename = __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';
            if (is_readable($filename)) {
                require $filename;
            }
        });
    }
}
