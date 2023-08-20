<?php

namespace App\System\Contracts;

use App\System\Router;

interface IModule
{
    public function registerRoutes(): void;
}