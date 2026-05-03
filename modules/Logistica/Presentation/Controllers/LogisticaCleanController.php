<?php

namespace Modules\Logistica\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Logistica\Application\UseCases\ObtenerContextoLogisticaCasoDeUso;

class LogisticaCleanController extends Controller
{
    public function __construct(private ObtenerContextoLogisticaCasoDeUso $casoDeUso)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->casoDeUso->ejecutar()->aArray());
    }

    public function index(): View
    {
        return view('module-logistica::index', [
            'context' => $this->casoDeUso->ejecutar()->aArray(),
        ]);
    }
}
