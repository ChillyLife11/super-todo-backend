<?php

class Todo extends Base
{

    public string $tableName = 'todos';
    public array $fieldNames = ['name', 'done'];

    public function __construct(\App\System\Database $db, \Faker\Generator $faker)
    {
        parent::__construct($db, $faker);

        for ($a = 0; $a < 20; $a++) {
            $this->fields[] = [
                'name' => $this->faker->words(mt_rand(1, 6), true),
                'done' => mt_rand(0, 1) ? 0 : 1
            ];
        }
    }
}