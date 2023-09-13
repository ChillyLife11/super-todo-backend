<?php

namespace App\System;
class Router
{
    public static array $routes = [];
    protected static array $routeNames = [];

    public static function add(string $routeName, string $routeController): void
    {
        self::$routeNames[] = ['name' => $routeName, 'controller' => $routeController];
    }

    public static function buildRoutes(): void
    {
        foreach (self::$routeNames as $route) {
            self::get(new Route("/^{$route['name']}\/?$/", $route['controller']));

            self::get(new Route("/^{$route['name']}\/([0-9]+)\/?$/", $route['controller'], 'one', [
                1 => 'id',
            ]));

            self::post(new Route("/^{$route['name']}\/?$/", $route['controller'], 'add'));

            self::delete(new Route("/^{$route['name']}\/([0-9]+)\/?$/", $route['controller'], 'delete', [
                1 => 'id',
            ]));

            self::patch(new Route("/^{$route['name']}\/([0-9]+)\/?$/", $route['controller'], 'edit', [
                1 => 'id',
            ]));
        }
    }

    public static function matchRoute(string $url, string $requestMethod)
    {
        $baseRoute = null;
        $hasMatch = false;
        $matches = [];

        foreach (self::$routes as $route) {
            if (preg_match($route->test, $url, $matches)) {
                $hasMatch = true;
                if ($requestMethod === $route->requestMethod) {
                    $baseRoute = $route;
                    foreach ($route->params as $k => $v) {
                        if (isset($matches[$k])) {
                            $baseRoute->params[$v] = $matches[$k];
                        }
                    }
                }
            }
        }

        if ($hasMatch && $baseRoute === null) {
            http_response_code(405);
            if (isset($matches[1])) {
                header('Allow: GET, PATCH, DELETE');
            } else {
                header('Allow: GET, POST');
            }
            throw new \Exception('Wrong method used');
        }

        if ($baseRoute === null) {
            http_response_code(404);
            throw new \Exception('Resouce not found');
        }

        return $baseRoute;
    }

    public static function get(Route $route): void
    {
        $route->requestMethod = 'GET';
        self::$routes[] = $route;
    }

    public static function post(Route $route): void
    {
        $route->requestMethod = 'POST';
        self::$routes[] = $route;
    }

    public static function delete(Route $route): void
    {
        $route->requestMethod = 'DELETE';
        self::$routes[] = $route;
    }

    public static function patch(Route $route): void
    {
        $route->requestMethod = 'PATCH';
        self::$routes[] = $route;
    }

}