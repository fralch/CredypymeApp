<?php

namespace App\Console\Commands;

use App\MultiAgencia\ResolvedorConexionAgencia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedModulesCommand extends Command
{
    protected $signature = 'seed:modules
                            {--module=* : Modulos a ejecutar (creditos,general,gth,logistica,aplicacion)}
                            {--force : Forzar ejecucion en produccion}';

    protected $description = 'Ejecuta seeders modulares por lote o por modulo.';

    public function __construct(private readonly ResolvedorConexionAgencia $resolvedorConexionAgencia)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->resolvedorConexionAgencia->prepararConexionesDinamicasDesdeTablaAgencias();

        $map = $this->moduleSeeders();
        $selected = $this->selectedModules(array_keys($map));

        $exit = self::SUCCESS;

        foreach ($selected as $module) {
            $this->line('');
            $this->info("Modulo: {$module}");

            foreach ($map[$module] as $seederClass) {
                $this->line(" - seed => {$seederClass}");
                try {
                    $code = (int) Artisan::call('db:seed', [
                        '--class' => $seederClass,
                        '--force' => (bool) $this->option('force'),
                    ], $this->output);

                    if ($code !== self::SUCCESS) {
                        $exit = self::FAILURE;
                        $this->error("   Fallo en {$seederClass}");
                    }
                } catch (\Throwable $e) {
                    $exit = self::FAILURE;
                    $this->error('   Error: ' . $e->getMessage());
                }
            }
        }

        $this->line('');
        if ($exit === self::SUCCESS) {
            $this->info('Seeders modulares finalizados correctamente.');
        }

        return $exit;
    }

    /**
     * @return array<string, array<int, class-string>>
     */
    private function moduleSeeders(): array
    {
        return [
            'aplicacion' => [],
            'creditos' => [
                \Modules\Creditos\Infrastructure\Persistence\Seeders\CreditosCatalogosSeeder::class,
                \Modules\Creditos\Infrastructure\Persistence\Seeders\CreditosPermisosSeeder::class,
            ],
            'general' => [
                \Modules\General\Infrastructure\Persistence\Seeders\GeneralBootstrapSeeder::class,
            ],
            'gth' => [
                \Modules\Gth\Infrastructure\Persistence\Seeders\GthPermisosSeeder::class,
            ],
            'logistica' => [
                \Modules\Logistica\Infrastructure\Persistence\Seeders\LogisticaPermisosSeeder::class,
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
}
