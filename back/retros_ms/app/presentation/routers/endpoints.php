<?php

use App\Presentation\Repositories\SprintRepository;
use Slim\App;

return function (App $app) {
    $app->get('/sprints', [SprintRepository::class, 'all']);
    $app->get('/sprints/{id:[0-9]+}', [SprintRepository::class, 'find']);
    $app->get('/sprints/{id:[0-9]+}/anterior', [SprintRepository::class, 'previous']);
    $app->post('/sprints', [SprintRepository::class, 'create']);
    $app->put('/sprints/{id:[0-9]+}', [SprintRepository::class, 'update']);
    $app->delete('/sprints/{id:[0-9]+}', [SprintRepository::class, 'delete']);
};
