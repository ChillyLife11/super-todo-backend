<?php

namespace App\System;
class Router
{
    public static array $routes = [];

    public static function get(Route $route): void
    {
        self::$routes['GET'][] = $route;
    }
    public static function post(Route $route): void
    {
        self::$routes['POST'][] = $route;
    }
    public static function delete(Route $route): void
    {
        self::$routes['DELETE'][] = $route;
    }
    public static function patch(Route $route): void
    {
        self::$routes['PATCH'][] = $route;
    }
    public static function matchRoute(string $url, string $requestMethod)
    {
        $baseRoute = null;
        foreach (self::$routes[$requestMethod] as $route) {
            if (preg_match($route->test, $url, $matches)) {
                if ($requestMethod !== $route->requestMethod) {
                    http_response_code(405);
                    header("Allow: {$route->requestMethod}");
                    throw new \Exception("Method $requestMethod not allowed by this route");
                    exit;
                }

                $baseRoute = $route;

                foreach ($route->params as $k => $v) {
                    if (isset($matches[$k])) {
                        $baseRoute->params[$v] = $matches[$k];
                    }
                }
            }
        }

        if ($baseRoute === null) {
            http_response_code(404);
            throw new \Exception('Resouce not found');
        }

        return $baseRoute;
    }

}