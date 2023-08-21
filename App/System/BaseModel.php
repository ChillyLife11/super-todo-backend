<?php

namespace App\System;

class BaseModel
{
    public $db;
    protected string $tableName = '';
    protected string $primaryKey = '';

    protected array $fields = [];

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

    public function add(array $fields): int
    {
        if (!$this->validateFields($fields)) {
            http_response_code(422);
            throw new \Exception('Wrong payload passed');
        }

        $this->fieldsToActiveRecord($fields);

        $masksValues = array_map(function($field) {
            return $field['value'];
        }, $this->fields);
        $sql = $this->buildInsertSql();

        $this->query($sql, $masksValues);
        return $this->db->lastInsertId();
    }

    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        $stmt = $this->query($sql, ['id' => $id]);
        if ($stmt->rowCount() > 0) {
            return true;
        } else  {
            throw new \Exception("Todo with id=$id not found to delete");
        }
    }

    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function validateFields(array $fields): bool
    {
        foreach ($fields as $key => $value) {
            if (!isset($this->fields[$key])) {
                return false;
            }
            if (!preg_match($this->fields[$key]['pattern'], $value)) {
                return false;
            }
        }

        return true;
    }

    protected function buildInsertSql(): string
    {
        $columns = implode(', ', array_keys($this->fields));
        $masks = ':' . implode(', :', array_keys($this->fields));

        return  "INSERT INTO {$this->tableName} ($columns) VALUES ($masks)";
    }
    protected function fieldsToActiveRecord(array $fields): void
    {
        foreach ($fields as $key => $value) {
            $this->fields[$key]['value'] = $value;
        }
    }
}