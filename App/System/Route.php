<?php

namespace App\System;

class Route
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