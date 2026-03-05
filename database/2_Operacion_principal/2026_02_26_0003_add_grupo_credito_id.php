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

        $agencias = Agencia::all();

        foreach ($agencias as $value) {

            $conexion = 'master_' . $value->id_agencia;

            //  DROP
            if (Schema::connection($conexion)->hasColumn('credito_registros', 'grupo_credito_id')) {

                Schema::connection($conexion)->table('credito_registros', function (Blueprint $table) {
                    $table->dropForeign(['grupo_credito_id']);
                    $table->dropColumn('grupo_credito_id');
                });
            }
            // CREATE
            if (!Schema::connection($conexion)->hasColumn('credito_registros', 'grupo_credito_id')) {

                Schema::connection($conexion)->table('credito_registros', function (Blueprint $table) {
                    $table->unsignedBigInteger('grupo_credito_id')->nullable()->after('aprobacion_id');
                    $table->foreign('grupo_credito_id')
                        ->references('id')
                        ->on('grupo_solicitud_creditos')
                        ->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
};
