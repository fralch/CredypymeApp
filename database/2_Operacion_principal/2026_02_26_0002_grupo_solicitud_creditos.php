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
            $conexion = 'main_' . $value->id_asgencia;

            Schema::connection($conexion)->disableForeignKeyConstraints();
            Schema::connection($conexion)->dropIfExists('credito_solicitudes');
            Schema::connection($conexion)->enableForeignKeyConstraints();

            Schema::connection($conexion)->create('grupo_solicitud_creditos', function (Blueprint $table) use ($main_db) {

                $table->id();
                $table->unsignedBigInteger('grupo_solicitud_id')->nullable();
                $table->unsignedBigInteger('agencia_cliente')->nullable();
                $table->unsignedBigInteger('cliente_id');
                $table->decimal('monto', 10, 2);
                $table->decimal('monto_retencion', 5, 2)->default(0);
                $table->decimal('cuota', 10, 2);
                $table->unsignedBigInteger('estado_id');

                $table->json('data_created')->nullable();
                $table->json('data_updated')->nullable();
                $table->timestamps();

                $table->foreign('grupo_solicitud_id')->references('id')->on('grupo_solicitudes');
                $table->foreign('agencia_cliente')->references('id')->on("$main_db.agencias");
                $table->foreign('cliente_id')->references('id')->on('clientes');
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
