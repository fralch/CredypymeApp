<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $main_db = env('S_MASTER_DATABASE');
        $agencias = Agencia::all();

        foreach ($agencias as  $value) {
            $conexion = 'master_' . $value->id_agencia;

            Schema::connection($conexion)->disableForeignKeyConstraints();
            Schema::connection($conexion)->dropIfExists('grupo_solicitud_creditos');
            Schema::connection($conexion)->enableForeignKeyConstraints();

            Schema::connection($conexion)->create('grupo_solicitud_creditos', function (Blueprint $table) use ($main_db) {

                $table->id();
                $table->unsignedBigInteger('grupo_solicitud_id')->nullable();
                $table->unsignedBigInteger('agencia_cliente')->nullable();
                $table->unsignedBigInteger('cliente_id');
                $table->decimal('monto', 10, 2)->default(0);
                $table->decimal('monto_retencion', 5, 2)->default(0);
                $table->decimal('cuota', 10, 2)->default(0);
                $table->unsignedBigInteger('estado_id');

                $table->longText('data_created')->nullable();
                $table->longText('data_updated')->nullable();
                $table->timestamps();

                $table->foreign('grupo_solicitud_id')->references('id')->on('grupo_solicitudes');
                $table->foreign('agencia_cliente')->references('id_agencia')->on("$main_db.agencias");
                $table->foreign('estado_id')->references('id')->on('credito_estados');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
};
