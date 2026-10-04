<?php

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

/**
 * ÚNICO lugar donde cambia el stock (RF12 y RF13).
 * Cada cambio = un movimiento + actualización de stock_actual, en una transacción
 * y con el producto bloqueado para evitar descuadres si dos personas registran a la vez.
 */
class InventarioService
{
    /** Quita ceros sobrantes: 10.00 -> 10, 2.50 -> 2.5 */
    public static function fmt($n): string
    {
        return rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
    }

    public function mover(
        int $productoId,
        string $tipo,            // 'entrada' | 'salida'
        float $cantidad,
        int $userId,
        string $fecha,
        ?string $motivo = null,
        array $origen = []       // donacion_id, ayuda_entregada_id, entrega_alimento_id
    ): MovimientoInventario {
        return DB::transaction(function () use ($productoId, $tipo, $cantidad, $userId, $fecha, $motivo, $origen) {
            $producto = Producto::lockForUpdate()->findOrFail($productoId);
            $actual = round((float) $producto->stock_actual, 2);
            $cantidad = round($cantidad, 2);

            if ($tipo === 'salida' && $actual < $cantidad) {
                throw new StockInsuficienteException(
                    "Stock insuficiente de «{$producto->nombre}»: hay ".self::fmt($actual)." {$producto->unidad_medida} y se necesitan ".self::fmt($cantidad).'.'
                );
            }

            $producto->stock_actual = $tipo === 'entrada' ? $actual + $cantidad : $actual - $cantidad;
            $producto->save();

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'user_id' => $userId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'fecha' => $fecha,
                'motivo' => $motivo,
            ] + array_intersect_key($origen, array_flip(['donacion_id', 'ayuda_entregada_id', 'entrega_alimento_id'])));
        });
    }
}
