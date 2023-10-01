<?php

namespace App\Modules\User;

use App\System\BaseModel;
use Firebase\JWT\JWT;

class Model extends BaseModel
{
    protected array $fields = [
        'name' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
        ],
        'username' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
        ],
        'password' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
        ],
    ];
    protected string $tableName = 'users';
    protected string $primaryKey = 'id_user';
    public function __construct()
    {
        parent::__construct();
    }

    public function getByUsername(array $fields): array|false
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE username = :username";
        $user = $this->db->query($sql, ['username' => $fields['username']])->fetch();

        if (!$user) {
            http_response_code(404);
            throw new \Exception('Wrong passed data');
        }

        if (!password_verify($fields['password'], $user['password'])) {
            http_response_code(401);
            throw new \Exception('Wrong passed data');
        }

        return $user;
    }

    public function add(array $fields): array
    {
        if (!$this->validateFields($fields)) {
            http_response_code(422);
            throw new \Exception('Incorrect payload');
        }

        $fields['password'] = password_hash($fields['password'], PASSWORD_BCRYPT);

        $columns = implode(', ', array_keys($fields));
        $masks = ':' . implode(', :', array_keys($fields));

        $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($masks)";

        $this->db->query($sql, $fields);

        return $this->one((string)$this->db->lastInsertId());
    }
}