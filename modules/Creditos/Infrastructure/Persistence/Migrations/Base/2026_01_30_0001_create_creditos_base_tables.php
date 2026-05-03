<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

return new class extends Migration
{
    public function up(): void
    {
        $agencias = Agencia::all();

        foreach ($agencias as $agencia) {
            $connection = 'master_' . $agencia->id_agencia;

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

                Schema::connection($connection)->table('credito_estados', function (Blueprint $table): void {
                    // placeholder to keep schema builder happy in some drivers
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
        }
    }

    public function down(): void
    {
        $agencias = Agencia::all();

        foreach ($agencias as $agencia) {
            $connection = 'master_' . $agencia->id_agencia;
            Schema::connection($connection)->dropIfExists('credito_registros');
            Schema::connection($connection)->dropIfExists('credito_estados');
            Schema::connection($connection)->dropIfExists('cliente_registros');
        }
    }
};

