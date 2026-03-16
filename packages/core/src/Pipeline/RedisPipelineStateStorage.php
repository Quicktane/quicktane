<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use DateTimeImmutable;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

class RedisPipelineStateStorage implements PipelineStateStorage
{
    public function save(PipelineState $state): void
    {
        $key = $this->key($state->token);
        $ttl = (int) config('pipelines.ttl', 3600);

        $data = json_encode([
            'token' => $state->token,
            'pipeline_name' => $state->pipelineName,
            'completed_steps' => $state->completedSteps,
            'current_step_index' => $state->currentStepIndex,
            'context' => $state->context->serialize(),
            'metadata' => $state->metadata,
            'reason' => $state->reason,
            'expires_at' => $state->expiresAt->format('Y-m-d H:i:s'),
        ], JSON_THROW_ON_ERROR);

        $this->connection()->setex($key, $ttl, $data);
    }

    public function load(string $token): ?PipelineState
    {
        $key = $this->key($token);
        /** @var string|null $data */
        $data = $this->connection()->get($key);

        if ($data === null) {
            return null;
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        return new PipelineState(
            token: (string) $decoded['token'],
            pipelineName: (string) $decoded['pipeline_name'],
            completedSteps: (array) $decoded['completed_steps'],
            currentStepIndex: (int) $decoded['current_step_index'],
            context: PipelineContext::deserialize((string) $decoded['context']),
            metadata: (array) $decoded['metadata'],
            reason: (string) $decoded['reason'],
            expiresAt: new DateTimeImmutable((string) $decoded['expires_at']),
        );
    }

    public function delete(string $token): void
    {
        $this->connection()->del($this->key($token));
    }

    private function key(string $token): string
    {
        return "pipeline:{$token}";
    }

    private function connection(): Connection
    {
        return Redis::connection();
    }
}
