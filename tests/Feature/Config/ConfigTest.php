<?php

namespace Tests\Feature\Config;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Config\Dto\ConfigDto;
use Quicktane\Core\Config\Enums\ConfigKey;
use Quicktane\Core\Config\Models\Config;
use Quicktane\Core\Config\Services\ConfigService;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    use WithFaker;

    protected ConfigService $configService;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->configService = resolve(ConfigService::class);
    }

    public function testList(): void
    {
        $configs = $this->configService->all();

        $this->assertTrue($configs->count() == Config::query()->count());
    }

    public function testCreateConfigValue(): void
    {
        $configDto = ConfigDto::fromArray([
            'key' => ConfigKey::GRADE,
            'value' => 'middle'
        ]);

        $config = $this->configService->set($configDto);

        $this->assertTrue($config->key == $configDto->key->value);
    }

    public function testGetConfig(): void
    {
        $configDto = ConfigDto::fromArray([
            'key' => ConfigKey::GRADE,
            'value' => 'middle'
        ]);

        $this->configService->set($configDto);
        $config = $this->configService->get(ConfigKey::GRADE);
        $config = $this->configService->getOrFail(ConfigKey::GRADE);

        $this->assertTrue('middle' === $config);
    }
}
