<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\General\Agencia;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $agencias = Agencia::all();

        foreach ($agencias as $item) {

            $agencia_id = $item->id_agencia;

            Schema::connection('master_' . $agencia_id)->table('cliente_parientes', function (Blueprint $table) {
                $table->dropForeign('cliente_parientes_agencia_pariente_foreign');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
