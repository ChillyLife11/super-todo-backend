<?php

namespace App\Ws;

use App\Modules\Todo\Ws as WsTodo;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require '../../vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WsTodo()
        )
    ),
    8080
);

$server->run();