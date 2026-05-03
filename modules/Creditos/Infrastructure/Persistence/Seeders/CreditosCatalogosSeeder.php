<?php

namespace Modules\Creditos\Infrastructure\Persistence\Seeders;

use App\MultiAgencia\ResolvedorConexionAgencia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditosCatalogosSeeder extends Seeder
{
    public function __construct(private readonly ResolvedorConexionAgencia $resolvedorConexionAgencia)
    {
    }

    public function run(): void
    {
        $agencias = DB::connection('master')->table('agencias')->pluck('id_agencia')->map(
            static fn ($id): int => (int) $id
        )->all();

        $estados = [
            ['id' => 1, 'estado' => 'PENDIENTE'],
            ['id' => 2, 'estado' => 'APROBADO'],
            ['id' => 3, 'estado' => 'DESEMBOLSADO'],
            ['id' => 4, 'estado' => 'DESAPROBADO'],
        ];

        foreach ($agencias as $idAgencia) {
            $connection = $this->resolvedorConexionAgencia->conexionMaster($idAgencia);

            foreach ($estados as $estado) {
                DB::connection($connection)->table('credito_estados')->updateOrInsert(
                    ['id' => $estado['id']],
                    [
                        'estado' => $estado['estado'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            DB::connection($connection)->table('cliente_registros')->updateOrInsert(
                ['id' => 1],
                ['nombres' => 'CLIENTE BASE', 'updated_at' => now(), 'created_at' => now()]
            );

            DB::connection($connection)->table('datos_aplicacion')->updateOrInsert(
                ['id' => 1],
                ['clave' => 'APP_THEME', 'valor' => 'default', 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
