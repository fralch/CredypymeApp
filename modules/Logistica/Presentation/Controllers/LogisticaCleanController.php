<?php

namespace Modules\Logistica\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Logistica\Application\UseCases\GetLogisticaContextUseCase;

class LogisticaCleanController extends Controller
{
    public function __construct(private GetLogisticaContextUseCase $useCase)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->useCase->execute()->toArray());
    }

    public function index(): View
    {
        return view('module-logistica::index', [
            'context' => $this->useCase->execute()->toArray(),
        ]);
    }
}
