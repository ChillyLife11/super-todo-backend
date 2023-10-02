<?php

namespace App\System;

class BaseModel
{
    protected Database $db;
    protected Auth $auth;
    protected string $tableName = '';
    protected string $primaryKey = '';

    protected array $fields = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->auth = Auth::getInstance();
    }

    public function all(array $params): ?array
    {
        $sql = "SELECT * FROM {$this->tableName}";

        $condPairs = [];
        $condStr = '';
        if (!empty($params)) {
            if (!$this->validateFields($params)) {
                http_response_code(422);
                throw new \Exception("Unprocessable Entity");
            }
    
            foreach ($params as $k => $v) {
                $condPairs[] .= $k . ' = :' . $k;
            }
            $condStr = implode(' AND ', $condPairs);
        }

        if ($this->auth::$userId !== null) {
            $condStr .= ($condStr === '' ? '' : ' AND ') . 'id_user=:id_user';
            $params = $params + ['id_user' => $this->auth::$userId];
        }

        if ($condStr !== '') {
            $sql .= " WHERE {$condStr}";
        }

        $sql .= " ORDER BY dt_add DESC";

        foreach ($this->fields as $k => $v) {
            if (array_key_exists($k, $params)) {
                if (array_key_exists('is_bool', $v)) {
                    $params[$k] = filter_var($params[$k], FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();

        foreach ($data as &$item) {
            foreach ($this->fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (array_key_exists('is_bool', $v)) {
                        $item[$k] = (bool) $item[$k];
                    }
                }
            }
        }

        return $data;
    }

    public function one(string $id): array
    {
        $params = [
            'id' => $id,
        ];

        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id";

        if ($this->auth::$userId !== null) {
            $sql .= ' AND id_user=:id_user';
            $params = $params + ['id_user' => $this->auth::$userId];
        }

        $stmt = $this->db->query($sql, $params);

        $one = $stmt->fetch();

        if (!$one) {
            http_response_code(404);
            throw new \Exception("With id=$id not found");
        }

        return $one;
    }

    public function edit(string $id, array $fields): bool
    {
        if (!$this->validateFields($fields)) {
            http_response_code(422);
            throw new \Exception('Incorrect payload');
        }

        $vals = implode(', ', array_map(function($item) {
            return $item . ' = :' . $item;
        }, array_keys($fields)));


        $sql = "UPDATE {$this->tableName} SET $vals WHERE {$this->primaryKey} = :id";

         if ($this->auth::$userId !== null) {
            $sql .= ' AND id_user=:id_user';
            $fields = $fields + ['id_user' => $this->auth::$userId];
        }

        $this->db->query($sql, $fields + ['id' => $id]);
        return true;
    }

    public function add(array $fields): array
    {
        if (!$this->validateFields($fields)) {
            http_response_code(422);
            throw new \Exception('Incorrect payload');
        }
        $columns = implode(', ', array_keys($fields));
        $masks = ':' . implode(', :', array_keys($fields));

        if ($this->auth::$userId !== null) {
            $columns .= ', id_user';
            $masks .= ', :id_user';
            $fields = $fields + ['id_user' => $this->auth::$userId];
        }

        $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($masks)";

        $this->db->query($sql, $fields);

        return $this->one((string)$this->db->lastInsertId());
    }

    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";

        $params = [
            'id' => $id,
        ];

        if ($this->auth::$userId !== null) {
            $sql .= ' AND id_user = :id_user';
            $params = $params + ['id_user' => $this->auth::$userId];
        }

        $stmt = $this->db->query($sql, $params);

        if ($stmt->rowCount() > 0) {
            return true;
        } else  {
            throw new \Exception("Data with id=$id not found to delete");
        }
    }

    public function validateFields(array $fields): bool
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

            if (!preg_match($this->fields[$key]['pattern'], $value)) {
                return false;
            }
        }

        return true;
    }
}