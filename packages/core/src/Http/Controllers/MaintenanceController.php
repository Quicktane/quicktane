<?php

declare(strict_types=1);

namespace Quicktane\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Quicktane\Core\Pipeline\PipelineManager;

class MaintenanceController extends Controller
{
    public function enable(): JsonResponse
    {
        Artisan::call('down');

        return response()->json(['message' => 'Maintenance mode enabled']);
    }

    public function status(PipelineManager $pipelineManager): JsonResponse
    {
        return response()->json([
            'maintenance' => app()->isDownForMaintenance(),
            'active_pipelines' => $pipelineManager->getActivePipelineCount(),
        ]);
    }

    public function disable(): JsonResponse
    {
        Artisan::call('up');

        return response()->json(['message' => 'Maintenance mode disabled']);
    }
}
