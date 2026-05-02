<?php

namespace Modules\Creditos\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Creditos\Application\UseCases\GetCreditosContextUseCase;

class CreditosCleanController extends Controller
{
    public function __construct(private GetCreditosContextUseCase $useCase)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->useCase->execute()->toArray());
    }

    public function index(): View
    {
        return view('module-creditos::index', [
            'context' => $this->useCase->execute()->toArray(),
        ]);
    }
}
