<?php

namespace App\Modules\Todo;

use \App\System\Contracts\IModule;
use App\System\Route;
use App\System\Router;

class Module implements IModule
{
    public string $name = 'todos';
    public string $class = Controller::class;

    public function registerMoreRoutes(): void
    {
//        Router::get(new Route("/^{$this->name}\/$/"));
    }
}