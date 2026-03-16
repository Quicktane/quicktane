<?php

declare(strict_types=1);

namespace Quicktane\Core\Http\Controllers;

use App\Store\Contracts\ConfigurationFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Quicktane\Core\Module\Config\ConfigField;
use Quicktane\Core\Module\Config\ConfigFieldType;
use Quicktane\Core\Module\ModuleInstaller;
use Quicktane\Core\Module\ModuleRegistry;
use Quicktane\Core\Module\ModuleServiceProvider;

class ModuleController
{
    public function index(ModuleRegistry $moduleRegistry, ModuleInstaller $moduleInstaller): JsonResponse
    {
        $modules = [];

        foreach ($moduleRegistry->all() as $name => $providerClass) {
            /** @var ModuleServiceProvider|null $provider */
            $provider = app()->getProvider($providerClass);

            if ($provider === null) {
                continue;
            }

            $modules[] = [
                'name' => $name,
                'version' => $provider->version(),
                'installed' => $moduleInstaller->isInstalled($name),
                'installed_version' => $moduleInstaller->getInstalledVersion($name),
                'dependencies' => $provider->dependencies(),
                'has_config' => $provider->configSchema() !== [],
                'has_demo_data' => $provider->demoSeeder() !== null,
            ];
        }

        return response()->json(['data' => $modules]);
    }

    public function config(
        string $module,
        ModuleRegistry $moduleRegistry,
        ConfigurationFacade $configurationFacade,
        Request $request,
    ): JsonResponse {
        $provider = $this->resolveProvider($moduleRegistry, $module);

        if ($provider === null) {
            return response()->json(['message' => "Module '{$module}' not found."], 404);
        }

        $schema = $provider->configSchema();

        if ($schema === []) {
            return response()->json(['message' => "Module '{$module}' has no configuration."], 404);
        }

        $scopeType = $request->query('scope', 'global');
        $scopeId = (int) $request->query('scope_id', '0');

        $fields = array_map(function (ConfigField $field) use ($configurationFacade, $module, $scopeType, $scopeId): array {
            $path = "{$module}/{$field->key}";
            $value = $configurationFacade->getValue($path, $scopeType, $scopeId, $field->default);

            $fieldData = $field->toArray();
            $fieldData['value'] = $field->type === ConfigFieldType::Encrypted && $value !== null
                ? '••••••••'
                : $value;

            return $fieldData;
        }, $schema);

        return response()->json(['data' => $fields]);
    }

    public function updateConfig(
        string $module,
        ModuleRegistry $moduleRegistry,
        ConfigurationFacade $configurationFacade,
        Request $request,
    ): JsonResponse {
        $provider = $this->resolveProvider($moduleRegistry, $module);

        if ($provider === null) {
            return response()->json(['message' => "Module '{$module}' not found."], 404);
        }

        $schema = $provider->configSchema();

        if ($schema === []) {
            return response()->json(['message' => "Module '{$module}' has no configuration."], 404);
        }

        $schemaKeys = array_map(fn (ConfigField $field): string => $field->key, $schema);
        $scopeType = $request->input('scope', 'global');
        $scopeId = (int) $request->input('scope_id', 0);
        $values = $request->input('values', []);

        foreach ($values as $key => $value) {
            if (! in_array($key, $schemaKeys, true)) {
                continue;
            }

            $path = "{$module}/{$key}";
            $configurationFacade->setValue($path, $value !== null ? (string) $value : null, $scopeType, $scopeId);
        }

        return response()->json(['message' => 'Configuration updated.']);
    }

    private function resolveProvider(ModuleRegistry $moduleRegistry, string $moduleName): ?ModuleServiceProvider
    {
        $providerClass = $moduleRegistry->get($moduleName);

        if ($providerClass === null) {
            return null;
        }

        /** @var ModuleServiceProvider|null $provider */
        $provider = app()->getProvider($providerClass);

        return $provider;
    }
}
