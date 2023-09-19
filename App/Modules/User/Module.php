<?php

namespace App\Modules\User;

use \App\System\Contracts\IModule;
use App\System\Route;
use App\System\Router;

class Module implements IModule
{
    public string $name = 'users';
    public string $class = Controller::class;

    public function registerMoreRoutes(): void
    {
        Router::post(new Route('/^users\/login$/', Controller::class, 'login'));
        Router::post(new Route('/^users\/refresh$/', Controller::class, 'refresh'));
        Router::post(new Route('/^users\/logout/', Controller::class, 'logout'));
    }
}