<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('auditoria_operaciones')) {
            Schema::create('auditoria_operaciones', function (Blueprint $table): void {
                $table->id();
                $table->string('modulo', 80);
                $table->string('codigo_operacion', 120);
                $table->unsignedBigInteger('agencia_id')->nullable();
                $table->string('usuario_id', 40)->nullable();
                $table->string('idempotencia_clave', 120)->nullable();
                $table->string('request_id', 120)->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('entidad', 120)->nullable();
                $table->string('entidad_id', 120)->nullable();
                $table->json('datos_entrada')->nullable();
                $table->json('datos_salida')->nullable();
                $table->json('datos_antes')->nullable();
                $table->json('datos_despues')->nullable();
                $table->string('estado', 40)->default('CONFIRMADO');
                $table->timestamps();

                $table->index(['modulo', 'codigo_operacion'], 'idx_auditoria_operacion');
                $table->index(['usuario_id', 'created_at'], 'idx_auditoria_usuario_fecha');
                $table->unique(['modulo', 'codigo_operacion', 'usuario_id', 'idempotencia_clave'], 'uk_auditoria_idempotencia');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_operaciones');
    }
};

