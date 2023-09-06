<?php

namespace App\Modules\Users;

use App\System\BaseController;
use App\System\BaseModel;
use Firebase\JWT\JWT;

class Controller extends BaseController
{
    protected BaseModel $model;
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
                'name'     => $user['name'],
                'username' => $user['username'],
                'dt_add'   => $user['dt_add']
            ], $_ENV['JWT_SECRET_KEY'], 'HS256');

            return json_encode(['access_token' => $accessToken]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}