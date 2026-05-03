<?php

namespace Tests\Unit;

use App\MultiAgencia\ResolvedorConexionAgencia;
use Tests\TestCase;

class ResolvedorConexionAgenciaTest extends TestCase
{
    public function testEnTestingConfiguraConexionDinamica(): void
    {
        $resolvedor = app(ResolvedorConexionAgencia::class);

        $this->assertSame('master_1', $resolvedor->conexionMaster(1));
        $this->assertIsArray(config('database.connections.master_1'));

        $this->assertSame('master_99', $resolvedor->conexionMaster(99));
        $this->assertIsArray(config('database.connections.master_99'));
    }

    public function testConfiguraConexionDinamicaEnProduccion(): void
    {
        $this->app->detectEnvironment(static fn () => 'production');

        config([
            'database.connections.master' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'database' => 'solucion_master',
                'username' => 'root',
                'password' => '',
            ],
            'database.connections.records' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'database' => 'solucion_records',
                'username' => 'root',
                'password' => '',
            ],
        ]);

        $resolvedor = app(ResolvedorConexionAgencia::class);

        $this->assertSame('master_2', $resolvedor->conexionMaster(2));
        $this->assertIsArray(config('database.connections.master_2'));
        $this->assertSame('solucion_master_2', config('database.connections.master_2.database'));

        $this->assertSame('records_2', $resolvedor->conexionRecords(2));
        $this->assertIsArray(config('database.connections.records_2'));
        $this->assertSame('solucion_records_2', config('database.connections.records_2.database'));
    }
}
