<?php

namespace App\controllers;

use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $api) {
    $api->group('/libros', function (RouteCollectorProxy $endpoint) {
        $endpoint->get('[/{id}]', Libro::class . ':read');
        $endpoint->post('', Libro::class . ':create');
        $endpoint->put('/{id}', Libro::class . ':update');
        $endpoint->delete('/{id}', Libro::class . ':delete');
    });
});
