<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceBudgetMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $elapsedMs = (microtime(true) - $startedAt) * 1000;
        $budgetMs = (float) config('nonfunctional.performance.page_load_budget_ms', 2000);

        $response->headers->set('Server-Timing', sprintf('app;dur=%.2f', $elapsedMs), false);
        $response->headers->set('X-Response-Time-Ms', number_format($elapsedMs, 2, '.', ''));
        $response->headers->set('X-Performance-Budget-Ms', number_format($budgetMs, 0, '.', ''));

        if ($elapsedMs > $budgetMs) {
            Log::warning('Page response exceeded performance budget.', [
                'method' => $request->method(),
                'path' => $request->path(),
                'elapsed_ms' => round($elapsedMs, 2),
                'budget_ms' => $budgetMs,
            ]);
        }

        return $response;
    }
}
