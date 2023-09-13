<?php

namespace App\System\Contracts;

interface IModule
{
    public function registerMoreRoutes(): void;
}