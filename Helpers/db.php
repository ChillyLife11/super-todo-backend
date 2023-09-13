<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$db = \App\System\Database::getInstance();
$faker = \Faker\Factory::create();

require './Base.php';
require './Todo.php';
require './User.php';


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