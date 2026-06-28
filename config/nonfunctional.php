<?php

return [
    'password' => [
        'min_length' => (int) env('PASSWORD_MIN_LENGTH', 8),
        'require_letters' => filter_var(env('PASSWORD_REQUIRE_LETTERS', true), FILTER_VALIDATE_BOOL),
        'require_numbers' => filter_var(env('PASSWORD_REQUIRE_NUMBERS', true), FILTER_VALIDATE_BOOL),
    ],

    'performance' => [
        'page_load_budget_ms' => (float) env('PERFORMANCE_BUDGET_MS', 2000),
    ],

    'reliability' => [
        'uptime_target_percent' => (float) env('UPTIME_TARGET_PERCENT', 99.5),
        'health_database_connections' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('HEALTH_DB_CONNECTIONS', 'default,supplier'))
        ))),
    ],
];
