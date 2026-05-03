<?php

namespace Modules\Gth\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Gth\Application\UseCases\ObtenerContextoGthCasoDeUso;

class GthCleanController extends Controller
{
    public function __construct(private ObtenerContextoGthCasoDeUso $casoDeUso)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->casoDeUso->ejecutar()->aArray());
    }

    public function index(): View
    {
        return view('module-gth::index', [
            'context' => $this->casoDeUso->ejecutar()->aArray(),
        ]);
    }
}
