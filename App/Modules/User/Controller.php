<?php

namespace App\Modules\User;

use App\System\BaseController;
use App\System\BaseModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Controller extends BaseController
{
    public BaseModel $model;
    public BaseModel $modelRefreshToken;
    public function __construct()
    {
        $this->model = new Model();
        $this->modelRefreshToken = new ModelRefreshToken();
    }

    public function login(): string
    {
        try {
            $fields = (array) json_decode(file_get_contents('php://input'), true);
            $user = $this->model->getByUsername($fields);

            $refreshExp = time() + 43200;

            $tokens = $this->getTokens($user, $refreshExp);

            $refreshTokenHash = $this->modelRefreshToken->getHash($tokens['refresh_token']);

            $this->modelRefreshToken->add(['token_hash' => $refreshTokenHash, 'expiry' => $refreshExp]);

            return json_encode($tokens);
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

        try {
            $payload = (array) JWT::decode($fields['token'], new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
            $userId = $payload['sub'];

            $user = $this->model->one((string) $userId);

            $refreshExp = time() + 43200;
            $tokens = $this->getTokens($user, $refreshExp);
            $refreshTokenHash = $this->modelRefreshToken->getHash($tokens['refresh_token']);

            $this->modelRefreshToken->deleteRefreshToken($this->modelRefreshToken->getHash($fields['token']));

            $this->modelRefreshToken->add(['token_hash' => $refreshTokenHash, 'expiry' => $refreshExp]);

            return json_encode($tokens);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    protected function getTokens(array $user, int $refreshExp): array
    {
        $accessToken = JWT::encode([
            'sub'      => $user['id_user'],
            'name'     => $user['name'],
            'username' => $user['username'],
            'dt_add'   => $user['dt_add'],
            'exp'      => time() + 20
        ], $_ENV['JWT_SECRET_KEY'], 'HS256');

        $refreshToken = JWT::encode([
            'sub' => $user['id_user'],
            'exp' => $refreshExp
        ], $_ENV['JWT_SECRET_KEY'], 'HS256');

        return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
    }
}