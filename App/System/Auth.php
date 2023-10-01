<?php

namespace App\System;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    public static ?string $userId = null;
    public static ?self $instance = null;
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function authAccessToken(): bool
    {
        if (!preg_match("/^Bearer\s+(.*)$/", $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            http_response_code(400);
            throw new \Exception('Incomplete authorization header');
            return false;
        }

        $data = (array) JWT::decode($matches[1], new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
        if ($data === null) {
            http_response_code(400);
            throw new \Exception('Invalid json');
            return false;
        }

        if (time() > $data['exp']) {
            http_response_code(401);
            throw new \Exception('Expired access token');
            return false;
        }

        self::$userId = (string) $data['sub'];

        return true;
    }
}