<?php

namespace App\System;

class BaseModel
{
    public Database $db;
    protected string $tableName = '';
    protected string $primaryKey = '';

    protected array $fields = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(array $params): ?array
    {
        $sql = '';
        
        if (!empty($params)) {
            if (!$this->validateFields($params)) {
                http_response_code(422);
                throw new \Exception("Unprocessable Entity");
            }

            $params = $this->toBool($params);
    
            $condPairs = [];
            foreach ($params as $k => $v) {
                $condPairs[] .= $k . ' = :' . $k;
            }
            $condStr = implode(' AND ', $condPairs);

            $sql = "SELECT * FROM {$this->tableName} WHERE {$condStr} ORDER BY dt_add DESC";
        } else  {
            $sql = "SELECT * FROM {$this->tableName} ORDER BY dt_add DESC";
        }

        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        foreach ($data as &$item) {
            $item = $this->toBool($item);
        }

        return $data;
    }

    public function one(string $id): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id ORDER BY dt_add DESC";
        $stmt = $this->db->query($sql, ['id' => $id]);
        $todo = $stmt->fetch();

        if (!$todo) {
            http_response_code(404);
            throw new \Exception("Todo with id=$id not found");
        }

        return $todo;
    }

    public function edit(string $id, array $fields): bool
    {
        if (!$this->validateFields($fields)) {
            http_response_code(422);
            throw new \Exception('Incorrect payload');
        }

        $sql = $this->buildUpdateSql($fields);

        $this->db->query($sql, $fields + ['id' => $id]);
        return true;
    }

    public function add(array $fields): array
    {
        if (!$this->validateFields($fields)) {
            http_response_code(422);
            throw new \Exception('Incorrect payload');
        }

        $sql = $this->buildInsertSql($fields);

        $this->db->query($sql, $fields);

        return $this->toBool($this->one((string)$this->db->lastInsertId()));
    }

    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else  {
            throw new \Exception("Todo with id=$id not found to delete");
        }
    }

    protected function toBool(array $fields): array
    {
        foreach ($fields as $k => &$v) {
            if ($k !== $this->primaryKey) {
                if ($v === 'true' || $v === '1' || $v === 1) $v = true;
                if ($v === 'false' || $v === '0' || $v === 0) $v = false;
            }
        }

        return $fields;
    }

    protected function validateFields(array $fields): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($this->fields as $key => $value) {
                if ($this->fields[$key]['required'] === true && !isset($fields[$key])) {
                    return false;
                }
            }
        }

        foreach ($fields as $key => $value) {
            if (!isset($this->fields[$key])) {
                return false;
            }
            var_dump($this->fields[$key]);
            if (!preg_match($this->fields[$key]['pattern'], $value)) {
                return false;
            }
        }

        return true;
    }

    protected function buildInsertSql(array $fields): string
    {
        $columns = implode(', ', array_keys($fields));
        $masks = ':' . implode(', :', array_keys($fields));

        return  "INSERT INTO {$this->tableName} ($columns) VALUES ($masks)";
    }

    protected function buildUpdateSql(array $fields): string
    {
        $vals = implode(', ', array_map(function($item) {
            return $item . ' = :' . $item;
        }, array_keys($fields)));


        return  "UPDATE {$this->tableName} SET $vals WHERE {$this->primaryKey} = :id";
    }
}