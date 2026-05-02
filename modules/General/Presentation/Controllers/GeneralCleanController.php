<?php

namespace Modules\General\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\General\Application\UseCases\GetGeneralContextUseCase;

class GeneralCleanController extends Controller
{
    public function __construct(private GetGeneralContextUseCase $useCase)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->useCase->execute()->toArray());
    }

    public function index(): View
    {
        return view('module-general::index', [
            'context' => $this->useCase->execute()->toArray(),
        ]);
    }
}
