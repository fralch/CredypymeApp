<?php

namespace Modules\General\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GeneralBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('master')->table('distritos')->updateOrInsert(
            ['id' => 1],
            ['distrito' => 'LIMA', 'updated_at' => now(), 'created_at' => now()]
        );

        DB::connection('master')->table('agencias')->updateOrInsert(
            ['id_agencia' => 1],
            [
                'nombre' => 'Agencia Principal',
                'direccion' => 'Sede principal',
                'distrito_id' => 1,
                'nueva_empresa' => 0,
            ]
        );

        DB::connection('master')->table('cargos')->updateOrInsert(
            ['id' => 1],
            [
                'cargo' => 'Administrador',
                'descripcion' => 'Acceso total para configuracion inicial',
                'jefatura' => 1,
                'habilitado' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::connection('master')->table('usuarios')->updateOrInsert(
            ['dni' => '00000001'],
            [
                'usuario' => 'admin',
                'clave' => Hash::make('admin123'),
                'nombres' => 'Usuario',
                'apellido_paterno' => 'Admin',
                'apellido_materno' => 'Local',
                'cargo_id' => 1,
                'agencia_id' => 1,
                'habilitado' => 1,
                'actualizo_clave' => 1,
                'usuario_real' => 'admin.local',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::connection('master')->table('versiones')->updateOrInsert(
            ['id_version' => 1],
            [
                'numero_version' => '1.0.0-local',
                'observaciones' => 'Bootstrap inicial modular',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::connection('master')->table('datos_aplicacion')->updateOrInsert(
            ['id' => 1],
            ['clave' => 'APP_THEME', 'valor' => 'default', 'updated_at' => now(), 'created_at' => now()]
        );

        $permisos = DB::connection('master')->table('permisos')->pluck('id')->all();
        foreach ($permisos as $permisoId) {
            DB::connection('master')->table('usuarios_permisos')->updateOrInsert(
                ['usuario_id' => '00000001', 'permiso_id' => $permisoId],
                [
                    'acceso_agencias' => json_encode([['agencia_id' => 1]], JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

