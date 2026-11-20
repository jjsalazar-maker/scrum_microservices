<?php

namespace App\Controllers;

use App\Controllers\Contracts\RetrosControllerInterface;
use App\Models\Retro;
use App\Validators\RetroValidator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class RetrosController implements RetrosControllerInterface
{
    private RetroValidator $validator;

    public function __construct(?RetroValidator $validator = null)
    {
        $this->validator = $validator ?? new RetroValidator();
    }

    public function get_retros(): Collection
    {
        return Retro::orderBy('id', 'asc')->get();
    }

    public function get_retros_by_sprint(int $sprint_id): Collection
    {
        return Retro::where('sprint_id', $sprint_id)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function get_acciones_by_sprint(int $sprint_id): Collection
    {
        return Retro::where('sprint_id', $sprint_id)
            ->where('categoria', 'accion')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function find_retro(int $id): Retro
    {
        return Retro::findOrFail($id);
    }

    public function create_retro(array $data): Retro
    {
        $sprint_id = $this->validator->validarSprintId($data['sprint_id'] ?? 0);
        $categoria = $this->validator->validarCategoria($data['categoria'] ?? '');
        $descripcion = $this->validator->validarDescripcion($data['descripcion'] ?? '');
        $fecha_revision = $this->validator->validarFechaRevision($data['fecha_revision'] ?? null);
        $cumplida = $this->validator->normalizarCumplida($categoria, $data['cumplida'] ?? null);

        $retro = new Retro();
        $retro->sprint_id = $sprint_id;
        $retro->categoria = $categoria;
        $retro->descripcion = $descripcion;
        $retro->cumplida = $cumplida;
        $retro->fecha_revision = $fecha_revision;
        $retro->save();

        return $retro;
    }

    public function update_retro(int $id, array $data): Retro
    {
        $retro = Retro::findOrFail($id);

        $categoria = $this->validator->validarCategoria($data['categoria'] ?? $retro->categoria);
        $descripcion = $this->validator->validarDescripcion($data['descripcion'] ?? $retro->descripcion);
        $fecha_revision = array_key_exists('fecha_revision', $data)
            ? $this->validator->validarFechaRevision($data['fecha_revision'])
            : $retro->fecha_revision;
        $cumplida = array_key_exists('cumplida', $data)
            ? $this->validator->normalizarCumplida($categoria, $data['cumplida'])
            : ($categoria === 'accion' ? $retro->cumplida : null);

        $retro->categoria = $categoria;
        $retro->descripcion = $descripcion;
        $retro->cumplida = $cumplida;
        $retro->fecha_revision = $fecha_revision;
        $retro->save();

        return $retro;
    }

    public function evaluate_accion(int $id, bool $cumplida, ?string $fecha_revision = null): Retro
    {
        $retro = Retro::findOrFail($id);

        if ($retro->categoria !== 'accion') {
            throw new InvalidArgumentException('Solo los ítems de categoría "accion" pueden evaluarse.');
        }

        $fecha = ($fecha_revision !== null && trim($fecha_revision) !== '')
            ? $this->validator->validarFechaRevision($fecha_revision)
            : date('Y-m-d');

        $retro->cumplida = $cumplida;
        $retro->fecha_revision = $fecha;
        $retro->save();

        return $retro;
    }

    public function delete_retro(int $id): Retro
    {
        $retro = Retro::findOrFail($id);
        $retro->delete();

        return $retro;
    }
}
