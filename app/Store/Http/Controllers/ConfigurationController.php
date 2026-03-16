<?php

declare(strict_types=1);

namespace App\Store\Http\Controllers;

use App\Store\Contracts\ConfigurationFacade;
use App\Store\Http\Requests\GetConfigurationRequest;
use App\Store\Http\Requests\SetConfigurationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConfigurationController extends Controller
{
    public function __construct(
        private readonly ConfigurationFacade $configurationFacade,
    ) {}

    public function show(GetConfigurationRequest $request): JsonResponse
    {
        $path = $request->validated('path');
        $scope = $request->validated('scope') ?? 'global';
        $scopeId = (int) ($request->validated('scope_id') ?? 0);

        $value = $this->configurationFacade->getValue($path, $scope, $scopeId);

        return response()->json([
            'path' => $path,
            'value' => $value,
            'scope' => $scope,
            'scope_id' => $scopeId,
        ]);
    }

    public function update(SetConfigurationRequest $request): JsonResponse
    {
        $path = $request->validated('path');
        $value = $request->validated('value');
        $scope = $request->validated('scope') ?? 'global';
        $scopeId = (int) ($request->validated('scope_id') ?? 0);

        $this->configurationFacade->setValue($path, $value, $scope, $scopeId);

        return response()->json([
            'path' => $path,
            'value' => $value,
            'scope' => $scope,
            'scope_id' => $scopeId,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
            'scope' => ['required', 'string', 'in:global,website,store,store_view'],
            'scope_id' => ['required', 'integer'],
        ]);

        $this->configurationFacade->deleteValue(
            $request->input('path'),
            $request->input('scope'),
            (int) $request->input('scope_id'),
        );

        return response()->json(null, 204);
    }
}
