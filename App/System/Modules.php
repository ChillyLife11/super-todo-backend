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

    public function modulesInit()
    {
        foreach ($this->modules as $module) {
            $module->registerRoutes();
        }
    }
}