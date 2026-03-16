<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Pipeline TTL (seconds)
    |--------------------------------------------------------------------------
    |
    | How long a suspended pipeline stays valid before expiring.
    | Can be set per-pipeline or use the default.
    |
    */
    'ttl' => [
        'default' => 3600,
        'checkout.place' => 1800,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Interval (minutes)
    |--------------------------------------------------------------------------
    |
    | How often the pipeline:cleanup command runs via scheduler.
    |
    */
    'cleanup_interval' => 1,

    /*
    |--------------------------------------------------------------------------
    | Non-Completable Steps
    |--------------------------------------------------------------------------
    |
    | Steps that cannot be force-completed. Pipelines suspended at these
    | steps will be skipped during forceCompleteAll().
    |
    */
    'non_completable_steps' => [],
];
