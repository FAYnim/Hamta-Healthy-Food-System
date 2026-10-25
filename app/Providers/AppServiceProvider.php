<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.admin.sidebar', function ($view): void {
            $logo = Schema::hasTable('pengaturan')
                ? Pengaturan::query()->value('logo')
                : null;

            $view->with('logo_pengaturan', $logo);
        });
    }
}
