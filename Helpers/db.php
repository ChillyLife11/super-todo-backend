<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$db = \App\System\Database::getInstance();
$faker = \Faker\Factory::create();

class Todo {
    public \App\System\Database $db;
    public \Faker\Generator $faker;

    public function __construct(\App\System\Database $db, \Faker\Generator $faker)
    {
        $this->db = $db;
        $this->faker = $faker;
    }

    public function clear(): string
    {
        $sql = "DELETE FROM todos";
        $this->db->query($sql);
        return 'Table successfully cleared';
    }

    public function fill(): string
    {
        $arr = [];

        for ($a = 0; $a < 20; $a++) {
            $arr[] = [
                'name' => $this->faker->words(mt_rand(1, 6), true),
                'done' => mt_rand(0, 1) ? 0 : 1
            ];
        }

        $vals = '';

        foreach ($arr as $item) {
            $vals .= ", ('" . implode("', '", array_values($item)) . "')";
        }

        $vals = mb_substr($vals, 1, mb_strlen($vals));

        $sql = "INSERT INTO todos (name, done) VALUES " . $vals;
        $this->db->query($sql);
        return 'Table successfully filled';
    }
}


if (!isset($argv[1])) {
    echo 'Please pass the class name';
    exit;
}
$class = $argv[1];

if (!class_exists($class)) {
    echo 'Please pass correct class name';
    exit;
}
$method = $argv[2];
if (!isset($argv[2])) {
    echo 'Please pass the method name';
    exit;
}
if (!method_exists($class, $method)) {
    echo 'Please pass correct method name';
    exit;
}

$todo = new $class($db, $faker);
echo $todo->$method();