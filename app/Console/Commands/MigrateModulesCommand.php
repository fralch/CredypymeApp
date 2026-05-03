<?php

namespace App\Console\Commands;

use App\MultiAgencia\ResolvedorConexionAgencia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateModulesCommand extends Command
{
    protected $signature = 'migrate:modules
                            {action=migrate : migrate|rollback|refresh|status}
                            {--module=* : Modulos a ejecutar (aplicacion,creditos,general,gth,logistica)}
                            {--step=1 : Pasos para rollback}
                            {--force : Forzar ejecucion en produccion}';

    protected $description = 'Ejecuta migraciones modulares por lote o por modulo.';

    public function __construct(private readonly ResolvedorConexionAgencia $resolvedorConexionAgencia)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->resolvedorConexionAgencia->prepararConexionesDinamicasDesdeTablaAgencias(true);

        $action = strtolower((string) $this->argument('action'));
        if (!in_array($action, ['migrate', 'rollback', 'refresh', 'status'], true)) {
            $this->error("Accion no valida: {$action}");
            return self::FAILURE;
        }

        $pathsByModule = $this->moduleMigrationPaths();
        $selected = $this->selectedModules(array_keys($pathsByModule));

        $exitCode = self::SUCCESS;

        foreach ($selected as $module) {
            $this->line('');
            $this->info("Modulo: {$module}");

            foreach ($pathsByModule[$module] as $absolutePath) {
                if (!is_dir($absolutePath)) {
                    continue;
                }

                $relativePath = str_replace('\\', '/', str_replace(base_path() . DIRECTORY_SEPARATOR, '', $absolutePath));
                $this->line(" - {$action} => {$relativePath}");

                $code = $this->runMigrationAction($action, $relativePath);
                if ($code !== self::SUCCESS) {
                    $exitCode = self::FAILURE;
                    $this->error("   Fallo en {$relativePath}");
                }
            }
        }

        $this->line('');
        if ($exitCode === self::SUCCESS) {
            $this->info('Migraciones modulares finalizadas correctamente.');
        }

        return $exitCode;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function moduleMigrationPaths(): array
    {
        return [
            'aplicacion' => [
                base_path('modules/Aplicacion/Infrastructure/Persistence/Migrations'),
            ],
            'creditos' => [
                base_path('modules/Creditos/Infrastructure/Persistence/Migrations/Base'),
                base_path('modules/Creditos/Infrastructure/Persistence/Migrations/Inicial'),
                base_path('modules/Creditos/Infrastructure/Persistence/Migrations/Principal'),
            ],
            'general' => [
                base_path('modules/General/Infrastructure/Persistence/Migrations'),
            ],
            'gth' => [
                base_path('modules/Gth/Infrastructure/Persistence/Migrations'),
            ],
            'logistica' => [
                base_path('modules/Logistica/Infrastructure/Persistence/Migrations'),
            ],
        ];
    }

    /**
     * @param array<int, string> $available
     * @return array<int, string>
     */
    private function selectedModules(array $available): array
    {
        $requested = array_map('strtolower', (array) $this->option('module'));
        $requested = array_values(array_filter($requested));

        if ($requested === []) {
            return $available;
        }

        return array_values(array_filter($available, static fn (string $module): bool => in_array($module, $requested, true)));
    }

    private function runMigrationAction(string $action, string $relativePath): int
    {
        try {
            $options = ['--path' => $relativePath];

            if ($action === 'rollback') {
                $options['--step'] = (int) $this->option('step');
            }

            if (in_array($action, ['migrate', 'rollback', 'refresh'], true)) {
                $options['--force'] = (bool) $this->option('force');
            }

            $command = match ($action) {
                'migrate' => 'migrate',
                'rollback' => 'migrate:rollback',
                'refresh' => 'migrate:refresh',
                'status' => 'migrate:status',
                default => 'migrate',
            };

            return (int) Artisan::call($command, $options, $this->output);
        } catch (\Throwable $e) {
            $this->error('   Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
