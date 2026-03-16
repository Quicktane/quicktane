<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Config;

class ConfigFieldBuilder
{
    private mixed $default = null;

    /** @var array<string, string> */
    private array $options = [];

    private bool $required = false;

    private ?string $description = null;

    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly ConfigFieldType $type,
    ) {}

    public function default(mixed $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @param  array<string, string>  $options
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function build(): ConfigField
    {
        return new ConfigField(
            key: $this->key,
            label: $this->label,
            type: $this->type,
            default: $this->default,
            options: $this->options,
            required: $this->required,
            description: $this->description,
        );
    }
}
