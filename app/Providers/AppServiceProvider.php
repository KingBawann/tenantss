<?php

namespace App\Providers;

use App\Listeners\LogSuccessfulLogin;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        // ── Policy Registration ───────────────────────────────────────
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        // ── Gate Definitions ──────────────────────────────────────────
        Gate::define('is-admin', fn (User $user) => $user->isAdmin() || $user->isOwner());
        Gate::define('is-cashier', fn (User $user) => $user->isCashier());
        Gate::define('has-admin-access', fn (User $user) => $user->hasAdminAccess());
        Gate::define('manage-users', fn (User $user) => $user->hasAdminAccess());

        // ── Event Listeners ───────────────────────────────────────────
        Event::listen(Login::class, LogSuccessfulLogin::class);

        // Auto-inject tenant_id for single-tenant mode compatibility
        Event::listen('eloquent.creating: *', function (string $eventName, array $data) {
            $model = $data[0] ?? null;
            if ($model instanceof \Illuminate\Database\Eloquent\Model && in_array('tenant_id', $model->getFillable()) && empty($model->tenant_id)) {
                $model->tenant_id = \Illuminate\Support\Facades\Auth::user()?->tenant_id ?? 1;
            }
        });
    }
}
