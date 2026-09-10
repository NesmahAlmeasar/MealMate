<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // public const HOME = 'user.role.redirect';
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
        // Register Meal Policy
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Meal::class, \App\Policies\MealPolicy::class);

        // Force custom pagination view
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.custom');
    }
}
