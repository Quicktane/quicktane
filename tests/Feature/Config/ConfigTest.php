<?php

namespace Tests\Feature\Config;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Config\Decorators\ConfigService;
use Quicktane\Core\Config\Enums\ConfigKey;
use Quicktane\Core\Config\Models\Config;
use Quicktane\Core\Config\Services\ConfigRepository;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    use WithFaker;

    protected ?ConfigRepository $configRepository = null;
    protected ?ConfigService $configService = null;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->configRepository = resolve(ConfigRepository::class);
        $this->configService = resolve(ConfigService::class);
    }

    public function testList(): void
    {
        $configs = $this->configService->all();

        $this->assertTrue($configs->count() == Config::query()->count());
    }

    public function testFind(): void
    {
        $this->createConfigValue();

        $config = $this->configService->find(ConfigKey::GRADE);

        $this->assertTrue($config == Config::query()->where('key', ConfigKey::GRADE->value)->first()->value);
    }

    public function testFindOrFail(): void
    {
        $this->createConfigValue();

        $config = $this->configService->findOrFail(ConfigKey::GRADE);

        $this->assertTrue($config == Config::query()->where('key', ConfigKey::GRADE->value)->first()->value);
    }

    public function testPut(): void
    {
        $config = [
            'key' => ConfigKey::GRADE,
            'value' => 'middle'
        ];

        $this->createConfigValue($config);

        $this->assertTrue($this->configService->find(ConfigKey::GRADE) == $config['value']);
    }

    public function testDelete(): void
    {
        $this->createConfigValue();
        $this->configService->delete(ConfigKey::GRADE);

        $this->assertTrue(Config::query()->where('key', ConfigKey::GRADE->name)->count() == 0);
    }

    protected function createConfigValue(?array $config = null): void
    {
        if (is_null($config)) {
            $config = [
                'key' => ConfigKey::GRADE,
                'value' => 'middle'
            ];
        }

        $this->configRepository->set($config);
    }
}
