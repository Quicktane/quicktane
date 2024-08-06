<?php

namespace Tests\Feature\Config;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Config\Decorators\ConfigDecorator;
use Quicktane\Core\Config\Dto\ConfigDto;
use Quicktane\Core\Config\Enums\ConfigKey;
use Quicktane\Core\Config\Models\Config;
use Quicktane\Core\Config\Services\ConfigService;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    use WithFaker;

    protected ?ConfigService $configService = null;
    protected ?ConfigDecorator $configDecorator = null;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->configService = resolve(ConfigService::class);
        $this->configDecorator = resolve(ConfigDecorator::class);
    }

    public function testList(): void
    {
        $configs = resolve(ConfigDecorator::class)->all();

        $this->assertTrue($configs->count() == Config::query()->count());
    }

    public function testFind(): void
    {
        $this->createConfigValue();

        $config = $this->configDecorator->find(ConfigKey::GRADE);

        $this->assertTrue($config == Config::query()->where('key', ConfigKey::GRADE->value)->first()->value);
    }

    public function testFindOrFail(): void
    {
        $this->createConfigValue();

        $config = $this->configDecorator->findOrFail(ConfigKey::GRADE);

        $this->assertTrue($config == Config::query()->where('key', ConfigKey::GRADE->value)->first()->value);
    }

    public function testPut(): void
    {
        $configDto = ConfigDto::fromArray([
            'key' => ConfigKey::GRADE,
            'value' => 'middle'
        ]);

        $this->createConfigValue($configDto);

        $this->assertTrue($this->configDecorator->find(ConfigKey::GRADE) == $configDto->value);
    }

    public function testDelete(): void
    {
        $this->createConfigValue();
        $this->configDecorator->delete(ConfigKey::GRADE);

        $this->assertTrue(Config::query()->where('key', ConfigKey::GRADE->name)->count() == 0);
    }

    protected function createConfigValue(?ConfigDto $configDto = null): void
    {
        if (is_null($configDto)) {
            $configDto = ConfigDto::fromArray([
                'key' => ConfigKey::GRADE,
                'value' => 'middle'
            ]);
        }

        $this->configService->set($configDto);
    }
}
