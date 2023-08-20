<?php

namespace App\System;

class BaseModel
{
    public $db;
    protected string $tableName = '';
    protected string $primaryKey = '';

    public function __construct()
    {
        $pdo = new Database($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
        $this->db = $pdo->getConnection();
    }

    public function all(): ?array
    {
        $sql = "SELECT * FROM {$this->tableName} ORDER BY dt_add DESC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    public function one(string $id): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id ORDER BY dt_add DESC";
        $stmt = $this->query($sql, ['id' => $id]);
        $todo = $stmt->fetch();

        if (!$todo) {
            http_response_code(404);
            throw new \Exception("Todo with id=$id not found");
        }

        return $todo;
    }


    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}