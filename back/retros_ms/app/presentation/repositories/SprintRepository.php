<?php

namespace app\presentation\repositories;

use App\Controllers\SprintsController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SprintRepository
{
    public function all(Request $request, Response $response, array $args): Response
    {
        $controller = new SprintsController();
        $rows = $controller->get_sprints();
        $response->getBody()->write($rows->toJson());
        return $response->withHeader("Content-Type", "application/json");
    }

    public function find(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $controller = new SprintsController();
            $row = $controller->find_sprint($id);
            $response->getBody()->write($row->toJson());
            return $response->withHeader("Content-Type", "application/json");
        } catch (ModelNotFoundException $ex) {
            return $response->withStatus(404);
        }
    }

    public function previous(Request $request, Response $response, array $args): Response
    {
        $id = (int)($args['id'] ?? 0);
        $controller = new SprintsController();
        $prev = $controller->get_previous_sprint($id);

        if ($prev === null) {
            $response->getBody()->write(json_encode(['sprint' => null, 'mensaje' => 'No hay sprint anterior.']));
            return $response->withHeader("Content-Type", "application/json");
        }

        $response->getBody()->write($prev->toJson());
        return $response->withHeader("Content-Type", "application/json");
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new SprintsController();
            $row = $controller->create_sprint($data);
            $response->getBody()->write($row->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(201);
        } catch (ModelNotFoundException $ex) {
            return $response->withStatus(406);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new SprintsController();
            $row = $controller->update($id, $data);
            $response->getBody()->write($row->toJson());
            return $response->withHeader("Content-Type", "application/json");
        } catch (ModelNotFoundException $ex) {
            return $response->withStatus(404);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $controller = new SprintsController();
            $row = $controller->delete($id);
            $response->getBody()->write($row->toJson());
            return $response->withHeader("Content-Type", "application/json");
        } catch (ModelNotFoundException $ex) {
            return $response->withStatus(404);
        }
    }
}
