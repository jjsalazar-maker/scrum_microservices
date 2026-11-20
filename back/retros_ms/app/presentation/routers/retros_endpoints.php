<?php

use App\Presentation\Repositories\RetroRepository;
use Slim\App;

return function (App $app) {
    $app->get('/retros', [RetroRepository::class, 'all']);
    $app->get('/sprints/{sprint_id}/retros', [RetroRepository::class, 'bySprint']);
    $app->get('/sprints/{sprint_id}/acciones', [RetroRepository::class, 'accionesBySprint']);
    $app->get('/retros/{id}', [RetroRepository::class, 'find']);
    $app->post('/retros', [RetroRepository::class, 'create']);
    $app->put('/retros/{id}', [RetroRepository::class, 'update']);
    $app->patch('/retros/{id}/evaluar', [RetroRepository::class, 'evaluate']);
    $app->delete('/retros/{id}', [RetroRepository::class, 'delete']);
};
