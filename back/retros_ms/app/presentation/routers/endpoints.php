<?php

use App\Presentation\Repositories\SprintRepository;
use App\Presentation\Repositories\RetroRepository;
use Slim\App;

return function (App $app) {

    // ==================== SPRINTS ====================
    $app->get('/sprints', [SprintRepository::class, 'all']);
    $app->post('/sprints', [SprintRepository::class, 'create']);
    $app->put('/sprints/{id}', [SprintRepository::class, 'update']);
    $app->delete('/sprints/{id}', [SprintRepository::class, 'delete']);

    // ==================== RETROS ====================
    $app->get('/retros', [RetroRepository::class, 'all']);
    $app->get('/retros/{id}', [RetroRepository::class, 'find']);
    $app->post('/retros', [RetroRepository::class, 'create']);
    $app->put('/retros/{id}', [RetroRepository::class, 'update']);
    $app->patch('/retros/{id}/evaluar', [RetroRepository::class, 'evaluar']);
    $app->delete('/retros/{id}', [RetroRepository::class, 'delete']);

    // ==================== RETROS POR SPRINT ====================
    $app->get('/sprints/{sprint_id}/retros', [RetroRepository::class, 'bySprintId']);
    $app->get('/sprints/{sprint_id}/acciones', [RetroRepository::class, 'accionesBySprintId']);
};
