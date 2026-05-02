<?php

namespace Modules\General\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Modules\Logistica\Presentation\Controllers\LogisticaController;
use Modules\General\Infrastructure\Persistence\Eloquent\Cierre;
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
