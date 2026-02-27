<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\General\Agencia;

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

            $this->down($conexion);

            Schema::connection($conexion)->create('grupo_solicitudes', function (Blueprint $table) use ($main_db) {

                $table->id();
                $table->unsignedBigInteger('grupo_id');
                $table->integer('plazo');
                $table->enum('periodo_pago', [
                    'DIARIO',
                    'SEMANAL',
                    'QUINCENAL',
                    'MENSUAL'
                ]);
                $table->decimal('tasa_interes', 5, 2)->default(0);
                $table->decimal('tasa_retencion', 5, 2)->default(0);
                $table->string('comentario_solicitud', 300)->nullable();
                $table->unsignedBigInteger('estado_id');
                $table->dateTime('fecha_solicitud')->nullable();
                $table->unsignedBigInteger('usuario_solicitud')->nullable();
                $table->dateTime('fecha_aprobacion')->nullable();
                $table->unsignedBigInteger('usuario_aprobacion')->nullable();
                $table->dateTime('fecha_desembolso')->nullable();
                $table->unsignedBigInteger('agencia_caja')->nullable();
                $table->unsignedBigInteger('caja_desembolso')->nullable();
                $table->datetime('fecha_desaprobacion')->nullable();
                $table->string('comentario_desaprobacion', 300)->nullable();
                $table->unsignedBigInteger('usuario_desaprobacion')->nullable();

                $table->json('datos_creacion')->nullable();
                $table->json('datos_actualizacion')->nullable();
                $table->timestamps();

                $table->foreign('agencia_id')->references('id')->on("$main_db.agencias");
                $table->foreign('grupo_id')->references('id')->on('grupos');
                $table->foreign('estado_id')->references('id')->on('credito_estados');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down($conexion)
    {
        Schema::connection($conexion)->disableForeignKeyConstraints();
        Schema::connection($conexion)->dropIfExists('cliente_grupo_solicitudes');
        Schema::connection($conexion)->enableForeignKeyConstraints();
    }
};
