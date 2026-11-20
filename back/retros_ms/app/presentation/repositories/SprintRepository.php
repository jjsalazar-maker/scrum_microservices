<?php

namespace App\Presentation\Repositories;

use App\Controllers\SprintsController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SprintRepository
{
    public function all(Request $request, Response $response, $args): Response
    {
        $controller = new SprintsController();
        $rows = $controller->get_sprints();
        $response->getBody()->write($rows->toJson());

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, $args): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new SprintsController();
            $row = $controller->create_sprint($data);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (InvalidArgumentException $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function update(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int) $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new SprintsController();
            $row = $controller->update_sprint($id, $data);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $ex) {
            $response->getBody()->write(json_encode(['error' => 'Sprint no encontrado.']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (InvalidArgumentException $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int) $args['id'];
            $controller = new SprintsController();
            $row = $controller->delete_sprint($id);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $ex) {
            $response->getBody()->write(json_encode(['error' => 'Sprint no encontrado.']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}
