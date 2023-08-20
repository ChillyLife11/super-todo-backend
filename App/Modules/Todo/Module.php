<?php

namespace App\Modules\Todo;

use \App\System\Contracts\IModule;
use \App\System\Router;
use \App\System\Route;

class Module implements IModule
{
    public function registerRoutes(): void
    {
        Router::get(new Route('/^todos\/?$/', Controller::class));
        Router::get(new Route('/^todos\/([0-9]+)\/?$/', Controller::class, 'one', [
            1 => 'id',
        ]));
        Router::delete(new Route('/^todos\/([0-9]+)\/?$/', Controller::class, 'one', [
            1 => 'id',
        ]));
    }
}