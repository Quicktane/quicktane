<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

class PipelineContext
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    public function serialize(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    public static function deserialize(string $json): self
    {
        $context = new self;
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $context->data = $data;

        return $context;
    }
}
