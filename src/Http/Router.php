<?php
/**
 * (c) Pierre-Henry Soria <hi@ph7.me> - All Rights Reserved.
 */

declare(strict_types=1);

namespace InstantWeb\Engine\Http;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Router
{
    public const GET_METHOD = 'GET';
    public const POST_METHOD = 'POST';
    public const GET_OR_POST_METHOD = 'GET_OR_POST';

    private const CONTROLLER_NAMESPACE = 'PH7App\Controller\\';
    private const SEPARATOR = '@';

    private static ?string $httpMethod;

    public static function get(string $uri, string $classMethod = ''): void
    {
        self::$httpMethod = self::GET_METHOD;
        self::run($uri, $classMethod);
    }

    public static function post(string $uri, string $classMethod = ''): void
    {
        self::$httpMethod = self::POST_METHOD;
        self::run($uri, $classMethod);
    }

    public static function getOrPost(string $uri, string $classMethod = ''): void
    {
        self::$httpMethod = self::GET_OR_POST_METHOD;

        self::run($uri, $classMethod);
    }

    public static function location(string $fromUri, string $toUrl): void
    {
        self::run($fromUri, $toUrl);
    }

    public static function isHomePage(): bool
    {
        return empty($_GET['uri']);
    }

    /**
     * @throws ReflectionException If the class or method doesn't exist.
     */
    private static function run(string $uri, string $value)
    {
        $uri = '/' . trim($uri, '/');
        $url = !empty($_GET['uri']) ? '/' . $_GET['uri'] : '/';

        if (preg_match("#^$uri$#", $url, $params)) {
            if (self::isRedirection($value)) {
                redirect($value);
            } elseif (self::isHttpMethodValid()) {
                $split = explode(self::SEPARATOR, $value);
                $className = self::CONTROLLER_NAMESPACE . $split[0];
                $method = $split[1];

                try {
                    $isEligibleMethod = class_exists($className) && (new ReflectionClass($className))->hasMethod(
                            $method
                        );
                    if ($isEligibleMethod) {
                        $action = new ReflectionMethod($className, $method);
                        if ($action->isPublic()) {
                            // And finally we perform the controller's action
                            return $action->invokeArgs(new $className, self::getActionParameters($params));
                        }
                        unset($action);
                    }
                } catch (ReflectionException $except) {
                    throw new HttpNotFoundException($except->getMessage());
                }
            }
            throw new HttpNotFoundException('Method "' . $method . '" was not found in "' . $class . '" class.');
        }
    }

    private static function isRedirection(string $method): bool
    {
        return !str_contains($method, self::SEPARATOR);
    }

    private static function isHttpMethodValid(): bool
    {
        if (self::$httpMethod === self::GET_OR_POST_METHOD) {
            return $_SERVER['REQUEST_METHOD'] === self::GET_METHOD || $_SERVER['REQUEST_METHOD'] === self::POST_METHOD;
        }

        return $_SERVER['REQUEST_METHOD'] === self::$httpMethod;
    }

    private static function getActionParameters(array $params): array
    {
        foreach ($params as $key => $val) {
            $params[$key] = str_replace('/', '', $val);
        }

        return $params;
    }
}
