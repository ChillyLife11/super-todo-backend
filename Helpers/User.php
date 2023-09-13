<?php

class User extends Base
{

    public string $tableName = 'users';
    public array $fieldNames = ['name', 'username', 'password'];

    public function __construct(\App\System\Database $db, \Faker\Generator $faker)
    {
        parent::__construct($db, $faker);

        for ($a = 0; $a < 20; $a++) {
            $this->fields[] = [
                'name'     => $this->faker->name(),
                'username' => $this->faker->userName(),
                'password' => password_hash((mt_rand(0, 1) ? 'pass1' : 'pass2'), PASSWORD_BCRYPT)
            ];
        }
    }
}