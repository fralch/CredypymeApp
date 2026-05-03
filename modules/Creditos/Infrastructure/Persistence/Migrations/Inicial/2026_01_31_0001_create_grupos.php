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
        $main_db = env('S_TEST_DATABASE', env('S_MASTER_DATABASE', 'solucion_master'));

        $agencias = Agencia::all();
        foreach ($agencias as $item) {

            $agencia_id = $item->id_agencia;

            Schema::connection('master_' . $agencia_id)->dropIfExists('grupos');

            Schema::connection('master_' . $agencia_id)->create('grupos', function (Blueprint $table) use ($main_db) {

                $table->id();
                $table->unsignedBigInteger('agencia_id')->nullable();
                $table->string('nombre', 200);
                $table->string('asesor_id', 20)->nullable();
                $table->boolean('habilitado')->default(1);

                $table->longText('datos_creacion')->nullable();
                $table->longText('datos_actualizacion')->nullable();
                $table->timestamps();

                $table->foreign('agencia_id')->references('id_agencia')->on("$main_db.agencias");
                $table->foreign('asesor_id')->references('dni')->on("$main_db.usuarios");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $agencias = Agencia::all();
        foreach ($agencias as $item) {
            $agencia_id = $item->id_agencia;
            Schema::connection('master_' . $agencia_id)->dropIfExists('grupos');
        }
    }
};
