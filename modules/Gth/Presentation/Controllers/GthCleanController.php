<?php

namespace Modules\Gth\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Gth\Application\UseCases\GetGthContextUseCase;

class GthCleanController extends Controller
{
    public function __construct(private GetGthContextUseCase $useCase)
    {
    }

    public function context(): JsonResponse
    {
        return response()->json($this->useCase->execute()->toArray());
    }

    public function index(): View
    {
        return view('module-gth::index', [
            'context' => $this->useCase->execute()->toArray(),
        ]);
    }
}
