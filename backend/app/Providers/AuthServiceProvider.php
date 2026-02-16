<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Policies\ContentPolicy;
use App\Models\Content;
use App\Policies\AdminContentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Content::class => ContentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage_users', function ($user) {
            // return $user->hasRole('admin'); //This is role based so paxi non admins le garna mildena.(super admin authority vs admin)
            return $user->can('manage_users');
        });
    }
}
