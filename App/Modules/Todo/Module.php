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

        Router::post(new Route('/^todos\/?$/', Controller::class, 'add'));

        Router::delete(new Route('/^todos\/([0-9]+)\/?$/', Controller::class, 'delete', [
            1 => 'id',
        ]));

        Router::patch(new Route('/^todos\/([0-9]+)\/?$/', Controller::class, 'edit', [
            1 => 'id',
        ]));
    }
}