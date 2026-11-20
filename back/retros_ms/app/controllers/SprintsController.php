<?php

namespace app\controllers;

use app\models\Sprint;
use Illuminate\Database\Eloquent\Collection;

class SprintsController
{
    public function get_sprints(): Collection
    {
        return Sprint::orderBy('id', 'desc')->get();
    }

    public function find_sprint(int $id): Sprint
    {
        return Sprint::where("id", $id)->firstOrFail();
    }

    public function get_previous_sprint(int $current_sprint_id): ?Sprint
    {
        return Sprint::where('id', '<', $current_sprint_id)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function find_previous_sprint(int $current_sprint_id): ?Sprint
    {
        return $this->get_previous_sprint($current_sprint_id);
    }

    public function create_sprint(array $data): Sprint
    {
        $sprint = new Sprint();
        $sprint->nombre = $data["nombre"];
        $sprint->fecha_inicio = $data["fecha_inicio"];
        $sprint->fecha_fin = $data["fecha_fin"];
        $sprint->save();
        return $sprint;
    }

    public function update(int $id, array $data): Sprint
    {
        $sprint = Sprint::findOrFail($id);
        $sprint->nombre = $data["nombre"];
        $sprint->fecha_inicio = $data["fecha_inicio"];
        $sprint->fecha_fin = $data["fecha_fin"];
        $sprint->save();
        return $sprint;
    }

    public function delete(int $id): Sprint
    {
        $sprint = Sprint::findOrFail($id);
        $sprint->delete();
        return $sprint;
    }
}
