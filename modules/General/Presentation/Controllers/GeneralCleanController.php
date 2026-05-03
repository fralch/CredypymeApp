<?php

namespace Modules\General\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\General\Application\UseCases\ObtenerContextoGeneralCasoDeUso;

class GeneralCleanController extends Controller
{
    public function __construct(private ObtenerContextoGeneralCasoDeUso $casoDeUso)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->casoDeUso->ejecutar()->aArray());
    }

    public function index(): View
    {
        return view('module-general::index', [
            'context' => $this->casoDeUso->ejecutar()->aArray(),
        ]);
    }
}
