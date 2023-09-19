<?php

namespace App\Modules\User;

use App\System\BaseModel;
use Firebase\JWT\JWT;

class ModelRefreshToken extends BaseModel
{
    protected array $fields = [
        'token_hash' => [
            'pattern' => '/^.{64,64}$/',
            'required' => true,
        ],
        'expiry' => [
            'pattern' => '/^\\d+$/',
            'required' => true,
        ],
    ];

    protected string $tableName = 'refresh_tokens';
    protected string $primaryKey = 'id_token';
    public function __construct()
    {
        parent::__construct();
    }

    public function getHash(string $refreshToken): string
    {
        return hash_hmac('sha256', $refreshToken, $_ENV['JWT_SECRET_KEY']);
    }

    public function deleteRefreshToken(string $token): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE token_hash = :token_hash";
        $stmt = $this->db->query($sql, ['token_hash' => $token]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else  {
            throw new \Exception("Data with token=$token not found to delete");
        }
    }

    public function getByToken(string $token): array | false
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE token_hash = :token";
        return $this->db->query($sql, ['token' => $token])->fetch();
    }
}