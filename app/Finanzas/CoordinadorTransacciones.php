<?php

namespace App\Finanzas;

use Illuminate\Support\Facades\DB;

class CoordinadorTransacciones
{
    public function ejecutar(array $nombresConexiones, callable $callback): mixed
    {
        $nombresConexiones = array_values(array_unique(array_map('strval', $nombresConexiones)));
        $conexiones = array_map(static fn (string $nombre) => DB::connection($nombre), $nombresConexiones);

        foreach ($conexiones as $conexion) {
            $conexion->beginTransaction();
        }

        try {
            $resultado = $callback();

            foreach (array_reverse($conexiones) as $conexion) {
                $conexion->commit();
            }

            return $resultado;
        } catch (\Throwable $e) {
            foreach (array_reverse($conexiones) as $conexion) {
                try {
                    $conexion->rollBack();
                } catch (\Throwable) {
                }
            }

            throw $e;
        }
    }
}

