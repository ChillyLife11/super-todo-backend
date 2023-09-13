<?php

namespace App\Modules\Users;

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
    }
}