<?php

namespace App\System;

use App\System\Contracts\IModule;

class Modules
{
    public array $modules = [];
    public function add(IModule $module): void
    {
        $this->modules[] = $module;
    }

    public function modulesInit(): void
    {
        foreach ($this->modules as $module) {
            Router::add($module->name, $module->class);
            $module->registerMoreRoutes();
        }

        Router::buildRoutes();
    }
}