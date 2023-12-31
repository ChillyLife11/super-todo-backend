<?php
require __DIR__ . '/init.php';

use App\System\Modules;
use App\System\Router;
use App\Modules\Todo\Module as Todo;
use App\Modules\User\Module as User;

try {
    $modules = new Modules();
    $modules->add(new Todo());
    $modules->add(new User());


    $modules->modulesInit();

    $url = $_GET['systemqueryurl'] ?? '';
    unset($_GET['systemqueryurl']);
    $activeRoute = Router::matchRoute($url, $_SERVER['REQUEST_METHOD']);


    $className = $activeRoute->controller;
    $class = new $className();
    $class->params = $activeRoute->params;
    $method = $activeRoute->method;
    echo $class->$method();
} catch (\Throwable $e) {
    echo json_encode([
        'message' => $e->getMessage(),
        'file'    => $e->getFile()
    ]);
}