<?php

namespace App\Controllers;

use App\Models\Sprint;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class SprintsController
{
    public function get_sprints(): Collection
    {
        return Sprint::orderBy('id', 'asc')->get();
    }

    public function find_sprint(int $id): Sprint
    {
        return Sprint::findOrFail($id);
    }

    public function create_sprint(array $data): Sprint
    {
        $nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
        if ($nombre === '') {
            throw new InvalidArgumentException('El campo nombre es obligatorio.');
        }

        $fecha_inicio = isset($data['fecha_inicio']) ? trim($data['fecha_inicio']) : '';
        if ($fecha_inicio === '') {
            throw new InvalidArgumentException('El campo fecha_inicio es obligatorio.');
        }

        $fecha_fin = isset($data['fecha_fin']) ? trim($data['fecha_fin']) : '';
        if ($fecha_fin === '') {
            throw new InvalidArgumentException('El campo fecha_fin es obligatorio.');
        }

        $sprint = new Sprint();
        $sprint->nombre = $nombre;
        $sprint->fecha_inicio = $fecha_inicio;
        $sprint->fecha_fin = $fecha_fin;
        $sprint->save();

        return $sprint;
    }

    public function update_sprint(int $id, array $data): Sprint
    {
        $sprint = Sprint::findOrFail($id);

        if (isset($data['nombre'])) {
            $nombre = trim($data['nombre']);
            if ($nombre === '') {
                throw new InvalidArgumentException('El campo nombre no puede estar vacío.');
            }
            $sprint->nombre = $nombre;
        }

        if (isset($data['fecha_inicio'])) {
            $sprint->fecha_inicio = trim($data['fecha_inicio']);
        }

        if (isset($data['fecha_fin'])) {
            $sprint->fecha_fin = trim($data['fecha_fin']);
        }

        $sprint->save();

        return $sprint;
    }

    public function delete_sprint(int $id): Sprint
    {
        $sprint = Sprint::findOrFail($id);
        $sprint->delete();

        return $sprint;
    }
}
