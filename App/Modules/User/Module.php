<?php

namespace App\Modules\User;

use App\System\Contracts\IModule;

class Module implements IModule
{
    public string $name = 'users';
    public string $class = Controller::class;

    public function registerMoreRoutes(): void
    {
//        Router::get(new Route("/^{$this->name}\/$/"));
    }
}