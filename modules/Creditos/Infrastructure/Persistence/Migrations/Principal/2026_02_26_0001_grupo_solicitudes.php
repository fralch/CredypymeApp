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
            Schema::connection($conexion)->dropIfExists('grupo_solicitudes');
            Schema::connection($conexion)->enableForeignKeyConstraints();

            Schema::connection($conexion)->create('grupo_solicitudes', function (Blueprint $table) use ($main_db) {

                $table->id();
                $table->unsignedBigInteger('grupo_id');
                $table->unsignedBigInteger('asesor_id')->nullable();
                $table->integer('plazo');
                $table->enum('periodo_pago', [
                    'SEMANAL',
                    'QUINCENAL',
                    'MENSUAL'
                ]);
                $table->decimal('tasa_interes', 5, 2)->default(0);
                $table->decimal('tasa_retencion', 5, 2)->default(0);
                $table->unsignedBigInteger('estado_id');
                $table->dateTime('fecha_solicitud')->nullable();
                $table->unsignedBigInteger('usuario_solicitud')->nullable();
                $table->dateTime('fecha_aprobacion')->nullable();
                $table->unsignedBigInteger('usuario_aprobacion')->nullable();
                $table->dateTime('fecha_desembolso')->nullable();
                $table->datetime('fecha_desaprobacion')->nullable();
                $table->string('comentario_desaprobacion', 300)->nullable();
                $table->unsignedBigInteger('usuario_desaprobacion')->nullable();

                $table->unsignedBigInteger('agencia_caja')->nullable();
                $table->unsignedBigInteger('caja_desembolso')->nullable();

                $table->longText('data_created')->nullable();
                $table->longText('data_updated')->nullable();
                $table->timestamps();

                $table->foreign('grupo_id')->references('id')->on('grupos');
                $table->foreign('asesor_id')->references('dni')->on("$main_db.usuarios");
                $table->foreign('estado_id')->references('id')->on('credito_estados');
                $table->foreign('usuario_solicitud')->references('dni')->on("$main_db.usuarios");
                $table->foreign('usuario_aprobacion')->references('dni')->on("$main_db.usuarios");
                $table->foreign('usuario_desaprobacion')->references('dni')->on("$main_db.usuarios");
                $table->foreign('agencia_caja')->references('id_agencia')->on("$main_db.agencias");
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
