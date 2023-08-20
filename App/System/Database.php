<?php

namespace App\System;

class Database
{
    protected ?\PDO $conn = null;
    public function __construct(
        protected string $host,
        protected string $user,
        protected string $password,
        protected string $name,
    )
    {}

    public function getConnection(): \PDO
    {
        if ($this->conn === null) {
            $this->conn = new \PDO("mysql:host={$this->host};dbname={$this->name}", $this->user, $this->password, [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
        }

        return $this->conn;
    }
}