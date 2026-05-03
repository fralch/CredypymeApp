<?php

namespace Modules\Gth\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GthPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensurePermisosTable();

        $rows = [
            ['area' => 'GTH', 'modulo' => 'PRINCIPAL'],

            ['area' => 'GTH_USUARIOS', 'modulo' => 'GESTION'],
            ['area' => 'GTH_USUARIOS', 'modulo' => 'HORARIOS'],
            ['area' => 'GTH_USUARIOS', 'modulo' => 'CESADOS'],
            ['area' => 'GTH_USUARIOS', 'modulo' => 'MI_INFORMACION'],

            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'ASISTENCIAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'TARDANZAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'FALTAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'JUSTIFICACIONES'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'REPORTE_GENERAL'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'PERMISOS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'LICENCIAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'MIS_ASISTENCIAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'MIS_TARDANZAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'MIS_FALTAS'],
            ['area' => 'GTH_ASISTENCIAS', 'modulo' => 'MIS_JUSTIFICACIONES'],

            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'PREGUNTAS'],
            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'EQUIPOS'],
            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'EXAMENES'],
            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'ASIGNAR_EVALUACION'],
            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'MIS_EVALUACIONES'],
            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'SEGUIMIENTO_EVALUACION'],
            ['area' => 'GTH_COLABORADOR_MES', 'modulo' => 'RESULTADOS_EVALUACION'],

            ['area' => 'GTH_PLANILLAS', 'modulo' => 'GESTION'],
            ['area' => 'GTH_PLANILLAS', 'modulo' => 'VACACIONES'],
            ['area' => 'GTH_PLANILLAS', 'modulo' => 'PLANILLA_GENERAL'],
            ['area' => 'GTH_PLANILLAS', 'modulo' => 'HISTORIAL_VACACIONES'],
            ['area' => 'GTH_PLANILLAS', 'modulo' => 'HISTORIAL_PLANILLAS'],

            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'ASISTENCIA_HORARIOS'],
            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'PLANILLA_SISTEMA_PENSIONES'],
            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'CIERRE_DIA'],
            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'COLABORADOR_MES'],
            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'COLABORADOR_MES_NIVELES'],
            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'COLABORADOR_MES_CATEGORIAS'],
            ['area' => 'GTH_MANTENIMIENTO', 'modulo' => 'LICENCIA_CATEGORIAS'],

            ['area' => 'GTH_APLICACION', 'modulo' => 'CIERRE_DIA'],
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

