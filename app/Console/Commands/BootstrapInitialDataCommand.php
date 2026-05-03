<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BootstrapInitialDataCommand extends Command
{
    protected $signature = 'bootstrap:initial-data {--force : Forzar ejecucion en produccion}';

    protected $description = 'Prepara el sistema desde cero: migraciones modulares + seeders modulares.';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->info('1) Ejecutando migraciones modulares...');
        $migrateCode = (int) Artisan::call('migrate:modules', [
            'action' => 'migrate',
            '--force' => $force,
        ], $this->output);

        if ($migrateCode !== self::SUCCESS) {
            $this->error('Fallaron migraciones modulares. Revisa la salida anterior.');
            return self::FAILURE;
        }

        $this->info('');
        $this->info('2) Ejecutando seeders modulares...');
        $seedCode = (int) Artisan::call('seed:modules', [
            '--force' => $force,
        ], $this->output);

        if ($seedCode !== self::SUCCESS) {
            $this->error('Fallaron seeders modulares. Revisa la salida anterior.');
            return self::FAILURE;
        }

        $this->info('');
        $this->info('Bootstrap completado correctamente.');
        $this->line('Credenciales iniciales: usuario=admin | clave=admin123');

        return self::SUCCESS;
    }
}

