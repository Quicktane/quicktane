<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SuspendedPipeline extends Model
{
    protected $fillable = [
        'token',
        'pipeline_name',
        'status',
        'customer_id',
        'guest_token',
        'context',
        'completed_steps',
        'current_step',
        'reason',
        'metadata',
        'result',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'context' => 'array',
            'completed_steps' => 'array',
            'metadata' => 'array',
            'result' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<SuspendedPipeline>  $query
     * @return Builder<SuspendedPipeline>
     */
    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', 'suspended');
    }

    /**
     * @param  Builder<SuspendedPipeline>  $query
     * @return Builder<SuspendedPipeline>
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'suspended')
            ->where('expires_at', '<', now());
    }
}
