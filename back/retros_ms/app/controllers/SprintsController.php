<?php

namespace app\controllers;

use app\models\Sprint;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class SprintsController
{
    function get_sprints(): Collection
    {
        return Sprint::all();
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
        return $sprint;
    }

    function update(int $id, array $data):Sprint{
        $sprint = Sprint::findOrFail($id);
        $sprint->nombre = $data["nombre"];
        $sprint->fecha_inicio = $data["fecha_inicio"];
        $sprint->fecha_fin = $data["fecha_fin"];
        $sprint->save();
        return $sprint;
    }

    function delete(int $id):Sprint{
        $sprint = Sprint::findOrFail($id);
        $sprint->delete();
        return $sprint;
    }

    function get_previous_sprint():{

        
    }
}