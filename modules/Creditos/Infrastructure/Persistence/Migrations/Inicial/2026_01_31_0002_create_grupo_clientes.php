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
        $agencias = Agencia::all();
        foreach ($agencias as $item) {

            $agencia_id = $item->id_agencia;

            Schema::connection('master_' . $agencia_id)->dropIfExists('grupo_clientes');

            Schema::connection('master_' . $agencia_id)->create('grupo_clientes', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('grupo_id')->nullable();
                $table->unsignedBigInteger('cliente_id')->nullable();
                $table->enum('responsable', ['R1', 'R2'])->nullable();

                $table->longText('datos_creacion')->nullable();
                $table->longText('datos_actualizacion')->nullable();

                $table->foreign('grupo_id')->references('id')->on('grupos');
                $table->foreign('cliente_id')->references('id')->on('cliente_registros');
                $table->timestamps();
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
