<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $agenciaIds = DB::connection('master')->table('agencias')->pluck('id_agencia')->map(
            static fn ($id): int => (int) $id
        )->all();

        foreach ($agenciaIds as $idAgencia) {
            $connection = 'master_' . $idAgencia;

            if (!Schema::connection($connection)->hasTable('cliente_registros')) {
                Schema::connection($connection)->create('cliente_registros', function (Blueprint $table): void {
                    $table->id();
                    $table->string('nombres', 150)->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::connection($connection)->hasTable('credito_estados')) {
                Schema::connection($connection)->create('credito_estados', function (Blueprint $table): void {
                    $table->id();
                    $table->string('estado', 120);
                    $table->timestamps();
                });
            }

            if (!Schema::connection($connection)->hasTable('credito_registros')) {
                Schema::connection($connection)->create('credito_registros', function (Blueprint $table): void {
                    $table->id();
                    $table->unsignedBigInteger('aprobacion_id')->nullable();
                    $table->timestamps();
                });
            } elseif (!Schema::connection($connection)->hasColumn('credito_registros', 'aprobacion_id')) {
                Schema::connection($connection)->table('credito_registros', function (Blueprint $table): void {
                    $table->unsignedBigInteger('aprobacion_id')->nullable()->after('id');
                });
            }

            if (!Schema::connection($connection)->hasTable('datos_aplicacion')) {
                Schema::connection($connection)->create('datos_aplicacion', function (Blueprint $table): void {
                    $table->id();
                    $table->string('clave', 120)->nullable();
                    $table->text('valor')->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::connection($connection)->hasTable('cuenta_usuarios')) {
                Schema::connection($connection)->create('cuenta_usuarios', function (Blueprint $table): void {
                    $table->id();
                    $table->string('dni', 20)->nullable();
                    $table->boolean('con_cuenta')->default(false);
                    $table->timestamps();
                });
            }

            if (!Schema::connection($connection)->hasTable('credito_carritos')) {
                Schema::connection($connection)->create('credito_carritos', function (Blueprint $table): void {
                    $table->id();
                    $table->string('usuario_id', 20)->nullable();
                    $table->dateTime('fecha_apertura')->nullable();
                    $table->dateTime('fecha_cierre')->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::connection($connection)->hasTable('caja_registros')) {
                Schema::connection($connection)->create('caja_registros', function (Blueprint $table): void {
                    $table->id();
                    $table->string('dni', 20)->nullable();
                    $table->longText('datos_cierre')->nullable();
                    $table->timestamps();
                });
            }
        }
    }

    public function down(): void
    {
    }
};

