<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding Interface with Implementation
        $this->app->bind(
            \App\Domain\Department\Repositories\DepartmentRepositoryInterface::class, 
            \App\Infrastructure\Persistence\Eloquent\Repositories\DepartmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
