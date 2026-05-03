<?php

namespace App\Auditoria;

use Illuminate\Support\Facades\DB;

class RegistradorAuditoria
{
    public function existeIdempotencia(string $modulo, string $codigoOperacion, ?string $usuarioId, ?string $clave): bool
    {
        if ($usuarioId === null || $clave === null || $clave === '') {
            return false;
        }

        return DB::table('auditoria_operaciones')
            ->where('modulo', $modulo)
            ->where('codigo_operacion', $codigoOperacion)
            ->where('usuario_id', $usuarioId)
            ->where('idempotencia_clave', $clave)
            ->exists();
    }

    public function registrar(array $data): void
    {
        DB::table('auditoria_operaciones')->insert([
            'modulo' => (string) ($data['modulo'] ?? 'GENERAL'),
            'codigo_operacion' => (string) ($data['codigo_operacion'] ?? 'OPERACION'),
            'agencia_id' => $data['agencia_id'] ?? null,
            'usuario_id' => $data['usuario_id'] ?? null,
            'idempotencia_clave' => $data['idempotencia_clave'] ?? null,
            'request_id' => $data['request_id'] ?? null,
            'ip' => $data['ip'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'entidad' => $data['entidad'] ?? null,
            'entidad_id' => $data['entidad_id'] ?? null,
            'datos_entrada' => isset($data['datos_entrada']) ? json_encode($data['datos_entrada']) : null,
            'datos_salida' => isset($data['datos_salida']) ? json_encode($data['datos_salida']) : null,
            'datos_antes' => isset($data['datos_antes']) ? json_encode($data['datos_antes']) : null,
            'datos_despues' => isset($data['datos_despues']) ? json_encode($data['datos_despues']) : null,
            'estado' => (string) ($data['estado'] ?? 'CONFIRMADO'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

