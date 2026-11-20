<?php

namespace App\Presentation\Repositories;

use App\Controllers\SprintsController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SprintRepository
{
    private SprintsController $controller;

    public function __construct(?SprintsController $controller = null)
    {
        $this->controller = $controller ?? new SprintsController();
    }

    public function all(Request $request, Response $response, array $args): Response
    {
        $rows = $this->controller->get_sprints();
        return $this->jsonResponse($response, $rows->toArray(), 200);
    }

    public function find(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $row = $this->controller->find_sprint($id);
            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Sprint no encontrado.'], 404);
        }
    }

    public function previous(Request $request, Response $response, array $args): Response
    {
        $id = (int)($args['id'] ?? 0);
        $prev = $this->controller->get_previous_sprint($id);

        if ($prev === null) {
            return $this->jsonResponse($response, ['sprint' => null, 'mensaje' => 'No hay sprint anterior.'], 200);
        }

        return $this->jsonResponse($response, $prev->toArray(), 200);
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $this->parseJsonBody($request);
            $row = $this->controller->create_sprint($data);
            return $this->jsonResponse($response, $row->toArray(), 201);
        } catch (InvalidArgumentException $ex) {
            return $this->jsonResponse($response, ['error' => $ex->getMessage()], 400);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $data = $this->parseJsonBody($request);
            $row = $this->controller->update_sprint($id, $data);
            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Sprint no encontrado.'], 404);
        } catch (InvalidArgumentException $ex) {
            return $this->jsonResponse($response, ['error' => $ex->getMessage()], 400);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $row = $this->controller->delete_sprint($id);
            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Sprint no encontrado.'], 404);
        }
    }

    private function parseJsonBody(Request $request): array
    {
        $body = $request->getBody()->getContents();
        if (trim($body) === '') {
            throw new InvalidArgumentException('El cuerpo de la petición JSON no puede estar vacío.');
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('JSON inválido en el cuerpo de la petición.');
        }

        return $data;
    }

    private function jsonResponse(Response $response, mixed $payload, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }
}
