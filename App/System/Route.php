<?php

namespace App\System;

use AllowDynamicProperties;

#[AllowDynamicProperties] class Route
{

    public function __construct(
        public string $test,
        public string $class,
        public string $method = 'index',
        public array $params = []
    )
    {
    }
}