<?php

namespace Modules\Creditos\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreditosPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensurePermisosTable();

        $rows = [
            ['area' => 'CREDITOS_CLIENTES', 'modulo' => 'GRUPOS'],
            ['area' => 'CREDITOS_CLIENTES', 'modulo' => 'GRUPOS_CLIENTES'],
            ['area' => 'CREDITOS_GRUPAL', 'modulo' => 'SOLICITUD'],
            ['area' => 'CREDITOS_GRUPAL', 'modulo' => 'COPIA_SOLICITUD'],
            ['area' => 'CREDITOS_GRUPAL', 'modulo' => 'APROBACION'],
            ['area' => 'CREDITOS_GRUPAL', 'modulo' => 'COPIA_APROBACION'],
            ['area' => 'CREDITOS_GRUPAL', 'modulo' => 'DOCUMENTOS'],
            ['area' => 'CREDITOS_GRUPAL', 'modulo' => 'DESEMBOLSO'],
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

