<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PredictionRequest;
use App\Services\PredictionService;
use Illuminate\Http\JsonResponse;

class PredictionApiController extends Controller
{
    protected PredictionService $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function predict(PredictionRequest $request): JsonResponse
    {
        $userId = $request->user() ? $request->user()->id : null;
        $result = $this->predictionService->predict($request->validated(), $userId);

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 200);
    }
}
