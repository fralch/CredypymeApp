<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Logistica\LogisticaController;
use App\Models\General\Cierre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CierreController extends Controller
{
    public function registrar($modulo_id)
    {

        $datos_registro = (new LogisticaController)->datos_registro();

        $cierre = Cierre::create(['modulo_id' => $modulo_id, 'datos_creacion' => $datos_registro]);

        $cierre_id = $cierre->id;

        return $cierre_id;
    }
}
