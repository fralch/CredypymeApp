<?php

namespace Modules\Logistica\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensurePermisosTable();

        $rows = [
            ['area' => 'LOGISTICA', 'modulo' => 'PRINCIPAL'],
            ['area' => 'LOGISTICA_APLICACION', 'modulo' => 'CIERRE_DIA'],

            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'HISTORIAL_COMPRAS'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'HISTORIAL_ENVIOS'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'HISTORIAL_ASIGNACIONES'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'HISTORIAL_VENTAS'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'HISTORIAL_DEPRECIACION'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'INVENTARIO'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'ASIGNACION'],
            ['area' => 'LOGISTICA_ACTIVOS', 'modulo' => 'VENTA'],

            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'ALMACEN'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'ASIGNACION'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'DEVOLUCION'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'COMPRAS'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'ENVIOS'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'TIPOS'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'PROVEEDORES'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'MEDICIONES'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'OPERACIONES'],
            ['area' => 'LOGISTICA_SUMINISTROS', 'modulo' => 'VENTAS'],
        ];

        foreach ($rows as $row) {
            DB::connection('master')->table('permisos')->updateOrInsert(
                ['area' => $row['area'], 'modulo' => $row['modulo']],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    private function ensurePermisosTable(): void
    {
        if (Schema::connection('master')->hasTable('permisos')) {
            return;
        }

        Schema::connection('master')->create('permisos', function (Blueprint $table): void {
            $table->id();
            $table->string('area', 120);
            $table->string('modulo', 120);
            $table->timestamps();
            $table->unique(['area', 'modulo'], 'uk_area_modulo');
        });
    }
}

