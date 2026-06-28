<?php

namespace App\Providers;

use App\Support\WindowsFriendlyFilesystem;
use Illuminate\Foundation\Vite;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->forgetInstance('files');

        $this->app->singleton('files', fn () => new WindowsFriendlyFilesystem);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Vite $vite): void
    {
        $vite->useHotFile(storage_path('framework/vite.hot'));

        Paginator::useBootstrapFive();

        Password::defaults(function (): Password {
            $rule = Password::min((int) config('nonfunctional.password.min_length', 8));

            if (config('nonfunctional.password.require_letters', true)) {
                $rule->letters();
            }

            if (config('nonfunctional.password.require_numbers', true)) {
                $rule->numbers();
            }

            return $rule;
        });
    }
}
