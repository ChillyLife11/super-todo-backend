<?php

abstract class Base
{
    public \App\System\Database $db;
    public \Faker\Generator $faker;

    public string $tableName = '';
    public array $fields = [];
    public array $fieldNames = [];

    public function __construct(\App\System\Database $db, \Faker\Generator $faker)
    {
        $this->db = $db;
        $this->faker = $faker;
    }

    public function clear(): string
    {
        $sql = "DELETE FROM {$this->tableName}";
        $this->db->query($sql);
        return 'Table successfully cleared';
    }

    public function fill(): string
    {

        $vals = '';
        foreach ($this->fields as $field) {
            $vals .= ", ('" . implode("', '", array_values($field)) . "')";
        }
        $vals = mb_substr($vals, 1, mb_strlen($vals));


        $names = implode(', ', $this->fieldNames);
        
        $sql = "INSERT INTO {$this->tableName} (" . $names . ") VALUES " . $vals;
        $this->db->query($sql);
        return 'Table successfully filled';
    }
}