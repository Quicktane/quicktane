<?php

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Settings\Enums\SettingsKey;
use Quicktane\Core\Settings\Models\Settings;
use Quicktane\Core\Settings\Repositories\SettingsRepository;
use Quicktane\Core\Settings\Services\SettingsService;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use WithFaker;

    protected ?SettingsRepository $settingsRepository = null;
    protected ?SettingsService $settingsService = null;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->settingsRepository = resolve(SettingsRepository::class);
        $this->settingsService = resolve(SettingsService::class);
    }

    public function testList(): void
    {
        $settings = $this->settingsService->all();

        $this->assertTrue($settings->count() == Settings::query()->count());
    }

    public function testFind(): void
    {
        $this->createSettingsValue();

        $settings = $this->settingsService->find(SettingsKey::GRADE);

        $this->assertTrue($settings == Settings::query()->where('key', SettingsKey::GRADE->value)->first()->value);
    }

    public function testFindOrFail(): void
    {
        $this->createSettingsValue();

        $settings = $this->settingsService->findOrFail(SettingsKey::GRADE);

        $this->assertTrue($settings == Settings::query()->where('key', SettingsKey::GRADE->value)->first()->value);
    }

    public function testPut(): void
    {
        $settings = [
            'key' => SettingsKey::GRADE,
            'value' => 'middle'
        ];

        $this->createSettingsValue($settings);

        $this->assertTrue($this->settingsService->find(SettingsKey::GRADE) == $settings['value']);
    }

    public function testDelete(): void
    {
        $this->createSettingsValue();
        $this->settingsService->delete(SettingsKey::GRADE);

        $this->assertTrue(Settings::query()->where('key', SettingsKey::GRADE->name)->count() == 0);
    }

    protected function createSettingsValue(?array $settings = null): void
    {
        if (is_null($settings)) {
            $settings = [
                'key' => SettingsKey::GRADE,
                'value' => 'middle'
            ];
        }

        if (!$this->settingsRepository->find(SettingsKey::GRADE)) {
            $this->settingsRepository->set($settings);
        }
    }
}
