<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = 'master_1';

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

    public function down(): void
    {
        $connection = 'master_1';

        Schema::connection($connection)->dropIfExists('caja_registros');
        Schema::connection($connection)->dropIfExists('credito_carritos');
        Schema::connection($connection)->dropIfExists('cuenta_usuarios');
        Schema::connection($connection)->dropIfExists('datos_aplicacion');
    }
};

