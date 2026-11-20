<?php

namespace App\Presentation\Repositories;

use App\Controllers\RetrosController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RetroRepository
{
    private RetrosController $controller;

    public function __construct(?RetrosController $controller = null)
    {
        $this->controller = $controller ?? new RetrosController();
    }

    public function all(Request $request, Response $response, array $args): Response
    {
        $rows = $this->controller->get_retros();
        return $this->jsonResponse($response, $rows->toArray(), 200);
    }

    public function bySprint(Request $request, Response $response, array $args): Response
    {
        $sprint_id = (int)($args['sprint_id'] ?? 0);
        if ($sprint_id <= 0) {
            return $this->jsonResponse($response, ['error' => 'El parámetro sprint_id es inválido.'], 400);
        }

        $rows = $this->controller->get_retros_by_sprint($sprint_id);
        return $this->jsonResponse($response, $rows->toArray(), 200);
    }

    public function accionesBySprint(Request $request, Response $response, array $args): Response
    {
        $sprint_id = (int)($args['sprint_id'] ?? 0);
        if ($sprint_id <= 0) {
            return $this->jsonResponse($response, ['error' => 'El parámetro sprint_id es inválido.'], 400);
        }

        $rows = $this->controller->get_acciones_by_sprint($sprint_id);
        return $this->jsonResponse($response, $rows->toArray(), 200);
    }

    public function find(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $row = $this->controller->find_retro($id);
            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Ítem de retrospectiva no encontrado.'], 404);
        }
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $this->parseJsonBody($request);
            $row = $this->controller->create_retro($data);
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
            $row = $this->controller->update_retro($id, $data);
            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Ítem de retrospectiva no encontrado.'], 404);
        } catch (InvalidArgumentException $ex) {
            return $this->jsonResponse($response, ['error' => $ex->getMessage()], 400);
        }
    }

    public function evaluate(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $data = $this->parseJsonBody($request);

            if (!array_key_exists('cumplida', $data)) {
                throw new InvalidArgumentException('El campo cumplida es obligatorio (true o false).');
            }

            $rawCumplida = $data['cumplida'];
            if (is_bool($rawCumplida)) {
                $cumplida = $rawCumplida;
            } elseif ($rawCumplida === 1 || $rawCumplida === '1' || $rawCumplida === 'true') {
                $cumplida = true;
            } elseif ($rawCumplida === 0 || $rawCumplida === '0' || $rawCumplida === 'false') {
                $cumplida = false;
            } else {
                throw new InvalidArgumentException('El campo cumplida debe ser booleano (true o false).');
            }

            $fecha_revision = isset($data['fecha_revision']) ? (string)$data['fecha_revision'] : null;
            $row = $this->controller->evaluate_accion($id, $cumplida, $fecha_revision);

            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Ítem de retrospectiva no encontrado.'], 404);
        } catch (InvalidArgumentException $ex) {
            return $this->jsonResponse($response, ['error' => $ex->getMessage()], 400);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $row = $this->controller->delete_retro($id);
            return $this->jsonResponse($response, $row->toArray(), 200);
        } catch (ModelNotFoundException $ex) {
            return $this->jsonResponse($response, ['error' => 'Ítem de retrospectiva no encontrado.'], 404);
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
