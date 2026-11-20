<?php

namespace App\Controllers\Contracts;

use App\Models\Retro;
use Illuminate\Database\Eloquent\Collection;

interface RetrosControllerInterface
{
    public function get_retros(): Collection;

    public function get_retros_by_sprint(int $sprint_id): Collection;

    public function get_acciones_by_sprint(int $sprint_id): Collection;

    public function find_retro(int $id): Retro;

    public function create_retro(array $data): Retro;

    public function update_retro(int $id, array $data): Retro;

    public function evaluate_accion(int $id, bool $cumplida, ?string $fecha_revision = null): Retro;

    public function delete_retro(int $id): Retro;
}
