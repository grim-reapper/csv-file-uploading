<?php

use Illuminate\Support\Str;

return [
    'domain' => env('HORIZON_DOMAIN'),
    'path' => 'horizon',
    'use' => 'default',
    'middleware' => ['web'],
    'prefix' => env('HORIZON_PREFIX', 'horizon:'),
    'waits' => [
        'redis:default' => 60,
    ],
    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],
    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'maxProcesses' => 10,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
        ],
        'local' => [
            'supervisor-1' => [
                'maxProcesses' => 3,
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'processes' => 2,
                'tries' => 3,
            ],
        ],
    ],
];