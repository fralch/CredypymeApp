<?php

namespace App\MultiAgencia;

use Illuminate\Support\Facades\DB;

class ResolvedorConexionAgencia
{
    public function conexionMaster(int $idAgencia): string
    {
        return $this->asegurarConexion("master_{$idAgencia}", 'master', "S_MASTER_DATABASE_{$idAgencia}", "solucion_master_{$idAgencia}");
    }

    public function conexionRecords(int $idAgencia): string
    {
        return $this->asegurarConexion("records_{$idAgencia}", 'records', "S_RECORDS_DATABASE_{$idAgencia}", "solucion_records_{$idAgencia}");
    }

    public function prepararConexionesDinamicasDesdeTablaAgencias(bool $crearBasesSiFaltan = false): void
    {
        $connections = (array) config('database.connections', []);
        if (!isset($connections['master'], $connections['records'])) {
            return;
        }

        try {
            $ids = DB::connection('master')->table('agencias')->pluck('id_agencia')->map(
                static fn ($id): int => (int) $id
            )->all();
        } catch (\Throwable) {
            return;
        }

        foreach ($ids as $idAgencia) {
            $masterDb = (string) env("S_MASTER_DATABASE_{$idAgencia}", "solucion_master_{$idAgencia}");
            $recordsDb = (string) env("S_RECORDS_DATABASE_{$idAgencia}", "solucion_records_{$idAgencia}");

            $this->asegurarConexion("master_{$idAgencia}", 'master', "S_MASTER_DATABASE_{$idAgencia}", $masterDb);
            $this->asegurarConexion("records_{$idAgencia}", 'records', "S_RECORDS_DATABASE_{$idAgencia}", $recordsDb);

            if ($crearBasesSiFaltan) {
                try {
                    DB::connection('master')->statement('CREATE DATABASE IF NOT EXISTS `' . $masterDb . '`');
                    DB::connection('master')->statement('CREATE DATABASE IF NOT EXISTS `' . $recordsDb . '`');
                } catch (\Throwable) {
                }
            }
        }
    }

    private function asegurarConexion(string $nombreConexion, string $nombreBase, string $envKey, string $dbPorDefecto): string
    {
        $configured = config("database.connections.{$nombreConexion}");
        if (is_array($configured) && !empty($configured)) {
            return $nombreConexion;
        }

        $base = config("database.connections.{$nombreBase}");
        if (!is_array($base) || empty($base)) {
            return $nombreBase;
        }

        $base['database'] = (string) env($envKey, $dbPorDefecto);
        config(["database.connections.{$nombreConexion}" => $base]);

        DB::purge($nombreConexion);

        return $nombreConexion;
    }
}
