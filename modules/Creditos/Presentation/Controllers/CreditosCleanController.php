<?php

namespace Modules\Creditos\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Creditos\Application\UseCases\ObtenerContextoCreditosCasoDeUso;

class CreditosCleanController extends Controller
{
    public function __construct(private ObtenerContextoCreditosCasoDeUso $casoDeUso)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->casoDeUso->ejecutar()->aArray());
    }

    public function index(): View
    {
        return view('module-creditos::index', [
            'context' => $this->casoDeUso->ejecutar()->aArray(),
        ]);
    }
}
