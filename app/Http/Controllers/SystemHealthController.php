<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SystemHealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $startedAt = microtime(true);

        $checks = [
            'application' => [
                'status' => 'ok',
                'name' => config('app.name'),
                'environment' => app()->environment(),
            ],
            'databases' => $this->checkDatabases(),
            'cache' => $this->checkCache(),
        ];

        $healthy = collect($checks['databases'])->every(fn (array $check): bool => $check['status'] === 'ok')
            && $checks['cache']['status'] === 'ok';

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'uptime_target_percent' => config('nonfunctional.reliability.uptime_target_percent', 99.5),
            'performance_budget_ms' => config('nonfunctional.performance.page_load_budget_ms', 2000),
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }

    private function checkDatabases(): array
    {
        $configuredConnections = config('nonfunctional.reliability.health_database_connections', ['default']);
        $connections = collect($configuredConnections)
            ->map(fn (string $connection): string => $connection === 'default' ? config('database.default') : $connection)
            ->filter()
            ->unique()
            ->values();

        return $connections
            ->mapWithKeys(fn (string $connection): array => [$connection => $this->checkDatabase($connection)])
            ->all();
    }

    private function checkDatabase(string $connection): array
    {
        $startedAt = microtime(true);

        try {
            DB::connection($connection)->select('select 1');

            return [
                'status' => 'ok',
                'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'fail',
                'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
                'error' => class_basename($exception),
                'message' => Str::limit($exception->getMessage(), 180),
            ];
        }
    }

    private function checkCache(): array
    {
        $startedAt = microtime(true);
        $key = 'health:'.(string) Str::uuid();

        try {
            Cache::put($key, 'ok', 30);
            $value = Cache::get($key);
            Cache::forget($key);

            return [
                'status' => $value === 'ok' ? 'ok' : 'fail',
                'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'fail',
                'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
                'error' => class_basename($exception),
                'message' => Str::limit($exception->getMessage(), 180),
            ];
        }
    }
}
