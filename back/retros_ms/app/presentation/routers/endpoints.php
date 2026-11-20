<?php

// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;
use App\Presentation\Repositories\SprintRepository;
use Slim\App;

return function (App $app) {
    // $app->get('/usuarios', function (Request $request, Response $response, $args) {
    //     $repository = new UserRepository();
    //     return $repository->all($request, $response, $args);
    // });
    
    $app->post('/login', [SprintRepository::class, 'login']);
    $app->get('/sprints', [SprintRepository::class, 'all']);
    $app->post('/sprints', [SprintRepository::class, 'create']);
    $app->put('/sprints/{id}', [SprintRepository::class, 'update']);
    $app->delete('/sprints/{id}', [SprintRepository::class, 'delete']);
};
