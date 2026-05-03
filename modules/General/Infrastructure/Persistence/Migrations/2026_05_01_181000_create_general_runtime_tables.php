<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('distritos')) {
            Schema::create('distritos', function (Blueprint $table): void {
                $table->id();
                $table->string('distrito', 120);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('datos_aplicacion')) {
            Schema::create('datos_aplicacion', function (Blueprint $table): void {
                $table->id();
                $table->string('clave', 120)->nullable();
                $table->text('valor')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table): void {
                $table->id();
                $table->string('area', 120);
                $table->string('modulo', 120);
                $table->string('datos_creacion', 255)->nullable();
                $table->string('datos_actualizacion', 255)->nullable();
                $table->timestamps();
                $table->unique(['area', 'modulo'], 'uk_area_modulo');
            });
        }

        if (!Schema::hasTable('usuarios_permisos')) {
            Schema::create('usuarios_permisos', function (Blueprint $table): void {
                $table->id();
                $table->string('usuario_id', 20);
                $table->unsignedBigInteger('permiso_id');
                $table->text('acceso_agencias')->nullable();
                $table->string('datos_creacion', 255)->nullable();
                $table->string('datos_actualizacion', 255)->nullable();
                $table->timestamps();

                $table->foreign('permiso_id')->references('id')->on('permisos');
                $table->index(['usuario_id', 'permiso_id'], 'idx_usuario_permiso');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_permisos');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('datos_aplicacion');
        Schema::dropIfExists('distritos');
    }
};

