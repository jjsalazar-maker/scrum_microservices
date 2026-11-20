<?php

namespace App\Validators;

use DateTime;
use InvalidArgumentException;

class RetroValidator
{
    public const CATEGORIAS_VALIDAS = [
        'accion',
        'logro',
        'impedimento',
        'comentario',
        'otro',
    ];

    public function validarSprintId(mixed $sprint_id): int
    {
        $valor = (int)$sprint_id;
        if ($valor <= 0) {
            throw new InvalidArgumentException('El campo sprint_id es obligatorio y debe ser un entero positivo.');
        }

        return $valor;
    }

    public function validarCategoria(mixed $categoria): string
    {
        $valor = is_string($categoria) ? trim($categoria) : '';
        if (!in_array($valor, self::CATEGORIAS_VALIDAS, true)) {
            throw new InvalidArgumentException(
                'Categoría inválida. Valores permitidos: ' . implode(', ', self::CATEGORIAS_VALIDAS) . '.'
            );
        }

        return $valor;
    }

    public function validarDescripcion(mixed $descripcion): string
    {
        $valor = is_string($descripcion) ? trim($descripcion) : '';
        if ($valor === '') {
            throw new InvalidArgumentException('La descripción es obligatoria.');
        }

        return $valor;
    }

    public function validarFechaRevision(mixed $fecha): ?string
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

    public function normalizarCumplida(string $categoria, mixed $cumplida): ?bool
    {
        if ($categoria !== 'accion' || $cumplida === null || $cumplida === '') {
            return null;
        }

        return $this->validarBooleanoCumplida($cumplida);
    }

    public function validarBooleanoCumplida(mixed $cumplida): bool
    {
        if (is_bool($cumplida)) {
            return $cumplida;
        }

        if ($cumplida === 1 || $cumplida === '1' || $cumplida === 'true') {
            return true;
        }

        if ($cumplida === 0 || $cumplida === '0' || $cumplida === 'false') {
            return false;
        }

        throw new InvalidArgumentException('El campo cumplida debe ser booleano (true o false).');
    }
}
