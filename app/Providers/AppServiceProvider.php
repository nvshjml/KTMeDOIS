<?php

namespace App\Providers;

use App\Support\WindowsFriendlyFilesystem;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

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
    }
}
