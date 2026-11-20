<?php

namespace app\controllers;

use app\models\Sprint;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class SprintsController
{
    function get_sprints(): Collection
    {
        return Spirnt::all();
    }

    function find_sprint(int $id): Sprint
    {

        return Sprint::where("id", $id)->firstOrFail();
    }

    function create_sprint(array $data): Sprint
    {
        $sprint = new Sprint();
        $sprint->nombre = $data["nombre"];
        $sprint->fecha_inicio = $data["fecha_inicio"];
        $sprint->fecha_fin = $data["fecha_fin"];
        $sprint->save();
        return $user;
    }

    function update(int $id, array $data):Sprint{
        $sprint = Sprint::findOrFail($id);
        $sprint->nombre = $data["nombre"];
        $sprint->fecha_inicio = $data["fecha_inicio"];
        $sprint->fecha_fin = $data["fecha_fin"];
        $sprint->save();
        return $sprint;
    }

    function delete(int $id):User{
        $sprint = Sprint::findOrFail($id);
        $sprint->delete();
        return $sprint;
    }
}