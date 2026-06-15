<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(fn () => Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers());

        Gate::define('permission', fn (User $user, string $permission) => $user->hasPermission($permission));

        View::composer('layouts.admin', function ($view) {
            if (! auth()->check() || ! auth()->user()->isStaffMember()) {
                return;
            }

            $view->with([
                'deliveryOverdueCount' => Order::deliveryOverdue()->count(),
                'deliveryDueSoonCount' => Order::deliveryDueSoon()->count(),
            ]);
        });
    }
}
