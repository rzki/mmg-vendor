<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;

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
        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true: null;
        });

        FilamentAsset::register([
            Css::make('change-font-size-css', __DIR__ . '/../../resources/css/override-filament.css'),
        ]);
    }
}
