<?php

namespace App\Presentation\Repositories;

use App\Controllers\RetrosController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RetroRepository
{
    /**
     * GET /retros — Listar todos los ítems de retrospectiva.
     */
    public function all(Request $request, Response $response, $args): Response
    {
        $controller = new RetrosController();
        $rows = $controller->get_retros();
        $response->getBody()->write($rows->toJson());

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * GET /sprints/{sprint_id}/retros — Listar los ítems de retro de un sprint.
     */
    public function bySprintId(Request $request, Response $response, $args): Response
    {
        try {
            $sprintId = (int) $args['sprint_id'];
            $controller = new RetrosController();
            $rows = $controller->get_retros_by_sprint($sprintId);
            $response->getBody()->write($rows->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (InvalidArgumentException $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /**
     * GET /sprints/{sprint_id}/acciones — Listar solo las acciones de un sprint.
     */
    public function accionesBySprintId(Request $request, Response $response, $args): Response
    {
        $sprintId = (int) $args['sprint_id'];
        $controller = new RetrosController();
        $rows = $controller->get_acciones_by_sprint($sprintId);
        $response->getBody()->write($rows->toJson());

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * GET /retros/{id} — Obtener un ítem de retro por su ID.
     */
    public function find(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int) $args['id'];
            $controller = new RetrosController();
            $row = $controller->find_retro($id);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $ex) {
            $response->getBody()->write(json_encode(['error' => 'Ítem de retrospectiva no encontrado.']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }

    /**
     * POST /retros — Crear un nuevo ítem de retrospectiva.
     */
    public function create(Request $request, Response $response, $args): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new RetrosController();
            $row = $controller->create_retro($data);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (InvalidArgumentException $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /**
     * PUT /retros/{id} — Actualizar un ítem de retrospectiva.
     */
    public function update(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int) $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new RetrosController();
            $row = $controller->update_retro($id, $data);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $ex) {
            $response->getBody()->write(json_encode(['error' => 'Ítem de retrospectiva no encontrado.']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (InvalidArgumentException $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /**
     * PATCH /retros/{id}/evaluar — Evaluar si una acción fue cumplida.
     */
    public function evaluar(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int) $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            $cumplida = $data['cumplida'] ?? null;
            if ($cumplida === null) {
                throw new InvalidArgumentException('El campo cumplida es obligatorio.');
            }

            $fecha_revision = $data['fecha_revision'] ?? null;

            $controller = new RetrosController();
            $row = $controller->evaluate_accion($id, (bool) $cumplida, $fecha_revision);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $ex) {
            $response->getBody()->write(json_encode(['error' => 'Ítem de retrospectiva no encontrado.']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (InvalidArgumentException $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /**
     * DELETE /retros/{id} — Eliminar un ítem de retrospectiva.
     */
    public function delete(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int) $args['id'];
            $controller = new RetrosController();
            $row = $controller->delete_retro($id);
            $response->getBody()->write($row->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $ex) {
            $response->getBody()->write(json_encode(['error' => 'Ítem de retrospectiva no encontrado.']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}
