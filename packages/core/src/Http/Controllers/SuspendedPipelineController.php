<?php

declare(strict_types=1);

namespace Quicktane\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Quicktane\Core\Pipeline\PipelineManager;
use Quicktane\Core\Pipeline\SuspendedPipeline;

class SuspendedPipelineController extends Controller
{
    public function index(PipelineManager $pipelineManager): JsonResponse
    {
        $pipelines = $pipelineManager->getSuspendedPipelines();

        return response()->json(['data' => $pipelines]);
    }

    public function show(string $token): JsonResponse
    {
        $pipeline = SuspendedPipeline::where('token', $token)->firstOrFail();

        return response()->json(['data' => $pipeline]);
    }

    public function expire(string $token): JsonResponse
    {
        $pipeline = SuspendedPipeline::where('token', $token)
            ->where('status', 'suspended')
            ->firstOrFail();

        $pipeline->update(['status' => 'expired']);

        return response()->json(['message' => 'Pipeline expired', 'token' => $token]);
    }

    public function forceCompleteAll(PipelineManager $pipelineManager): JsonResponse
    {
        $result = $pipelineManager->forceCompleteAll();

        return response()->json([
            'completed' => $result->completed,
            'blocked' => $result->blocked,
        ]);
    }
}
