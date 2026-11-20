<?php

namespace App\Controllers;

use App\Models\Retro;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class RetrosController
{
    public const CATEGORIAS_VALIDAS = [
        'accion',
        'logro',
        'impedimento',
        'comentario',
        'otro',
    ];

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
        $sprint_id = isset($data['sprint_id']) ? (int)$data['sprint_id'] : 0;
        if ($sprint_id <= 0) {
            throw new InvalidArgumentException('El campo sprint_id es obligatorio y debe ser un entero positivo.');
        }

        $categoria = $this->validarCategoria($data['categoria'] ?? '');
        $descripcion = $this->validarDescripcion($data['descripcion'] ?? '');
        $fecha_revision = $this->validarFechaRevision($data['fecha_revision'] ?? null);
        $cumplida = $this->normalizarCumplida($categoria, $data['cumplida'] ?? null);

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

        $categoria = $this->validarCategoria($data['categoria'] ?? $retro->categoria);
        $descripcion = $this->validarDescripcion($data['descripcion'] ?? $retro->descripcion);
        $fecha_revision = array_key_exists('fecha_revision', $data)
            ? $this->validarFechaRevision($data['fecha_revision'])
            : $retro->fecha_revision;
        $cumplida = array_key_exists('cumplida', $data)
            ? $this->normalizarCumplida($categoria, $data['cumplida'])
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
            ? $this->validarFechaRevision($fecha_revision)
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

    private function validarCategoria(mixed $categoria): string
    {
        $valor = is_string($categoria) ? trim($categoria) : '';
        if (!in_array($valor, self::CATEGORIAS_VALIDAS, true)) {
            throw new InvalidArgumentException(
                'Categoría inválida. Valores permitidos: ' . implode(', ', self::CATEGORIAS_VALIDAS) . '.'
            );
        }

        return $valor;
    }

    private function validarDescripcion(mixed $descripcion): string
    {
        $valor = is_string($descripcion) ? trim($descripcion) : '';
        if ($valor === '') {
            throw new InvalidArgumentException('La descripción es obligatoria.');
        }

        return $valor;
    }

    private function validarFechaRevision(mixed $fecha): ?string
    {
        if ($fecha === null || (is_string($fecha) && trim($fecha) === '')) {
            return null;
        }

        if (!is_string($fecha)) {
            throw new InvalidArgumentException('La fecha_revision debe tener formato YYYY-MM-DD.');
        }

        $valor = trim($fecha);
        $dt = DateTime::createFromFormat('Y-m-d', $valor);
        if (!$dt || $dt->format('Y-m-d') !== $valor) {
            throw new InvalidArgumentException('La fecha_revision debe tener formato YYYY-MM-DD.');
        }

        return $valor;
    }

    private function normalizarCumplida(string $categoria, mixed $cumplida): ?bool
    {
        if ($categoria !== 'accion' || $cumplida === null || $cumplida === '') {
            return null;
        }

        if (is_bool($cumplida)) {
            return $cumplida;
        }

        if ($cumplida === 1 || $cumplida === '1' || $cumplida === 'true') {
            return true;
        }

        if ($cumplida === 0 || $cumplida === '0' || $cumplida === 'false') {
            return false;
        }

        throw new InvalidArgumentException('El campo cumplida debe ser booleano (true/false) o null.');
    }
}
