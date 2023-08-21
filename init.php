<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');
    header('Content-type: application/json; charset=UTF-8');
    http_response_code(200); // Explicitly set the response code to 200
    exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-type: application/json; charset=UTF-8');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

require __DIR__ . '/config.php';
require BASE_DIR . '/vendor/autoload.php';

$dotenv->load();