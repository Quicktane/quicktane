<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Config;

readonly class ConfigField
{
    /**
     * @param  array<string, string>  $options
     */
    public function __construct(
        public string $key,
        public string $label,
        public ConfigFieldType $type,
        public mixed $default = null,
        public array $options = [],
        public bool $required = false,
        public ?string $description = null,
    ) {}

    public static function string(string $key, string $label): ConfigFieldBuilder
    {
        return new ConfigFieldBuilder($key, $label, ConfigFieldType::String);
    }

    public static function boolean(string $key, string $label): ConfigFieldBuilder
    {
        return new ConfigFieldBuilder($key, $label, ConfigFieldType::Boolean);
    }

    public static function integer(string $key, string $label): ConfigFieldBuilder
    {
        return new ConfigFieldBuilder($key, $label, ConfigFieldType::Integer);
    }

    public static function select(string $key, string $label): ConfigFieldBuilder
    {
        return new ConfigFieldBuilder($key, $label, ConfigFieldType::Select);
    }

    public static function encrypted(string $key, string $label): ConfigFieldBuilder
    {
        return new ConfigFieldBuilder($key, $label, ConfigFieldType::Encrypted);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type->value,
            'required' => $this->required,
        ];

        if ($this->default !== null) {
            $data['default'] = $this->default;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->options !== []) {
            $data['options'] = $this->options;
        }

        return $data;
    }
}
