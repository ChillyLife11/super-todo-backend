<?php

namespace App\Modules\Todo;

use \App\System\Contracts\IModule;

class Module implements IModule
{
    public string $name = 'todos';
    public string $class = Controller::class;

    public function registerMoreRoutes(): void
    {

    }
}