<?php

namespace App\Modules\Users;

use App\System\BaseController;
use App\System\BaseModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Controller extends BaseController
{
    public BaseModel $model;
    public function __construct()
    {
        $this->model = new Model();
    }

    public function login(): string
    {
        try {
            $fields = (array) json_decode(file_get_contents('php://input'), true);
            $user = $this->model->getByUsername($fields);

            $accessToken = JWT::encode([
                'sub'      => $user['id_user'],
                'name'     => $user['name'],
                'username' => $user['username'],
                'dt_add'   => $user['dt_add'],
                'exp'      => time() + 20
            ], $_ENV['JWT_SECRET_KEY'], 'HS256');

            $refreshToken = JWT::encode([
                'sub' => $user['id_user'],
                'exp' => time() + 43200
            ], $_ENV['JWT_SECRET_KEY'], 'HS256');

            return json_encode(['access_token' => $accessToken, 'refresh_token' => $refreshToken]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function refresh(): string
    {
        $fields = (array) json_decode(file_get_contents('php://input'), true);
        if (!array_key_exists('token', $fields)) {
            http_response_code(400);
            echo json_encode(['message' => 'missing token']);
            exit;
        }

//        try {
            $payload = (array) JWT::decode($fields['token'], new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
//        } catch (\Exception) {
//            http_response_code(400);
//            echo json_encode(['message' => 'invalid token']);
//            exit;
//        }

        $userId = $payload['sub'];

        $user = $this->model->one((string) $userId);

        $accessToken = JWT::encode([
            'sub'      => $user['id_user'],
            'name'     => $user['name'],
            'username' => $user['username'],
            'dt_add'   => $user['dt_add'],
            'exp'      => time() + 20
        ], $_ENV['JWT_SECRET_KEY'], 'HS256');

        $refreshToken = JWT::encode([
            'sub' => $user['id_user'],
            'exp' => time() + 43200
        ], $_ENV['JWT_SECRET_KEY'], 'HS256');

        return json_encode(['access_token' => $accessToken, 'refresh_token' => $refreshToken]);
    }
}