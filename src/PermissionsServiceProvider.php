<?php

namespace dastiii\Permissions;

use dastiii\Permissions\Models\Role;
use dastiii\Permissions\Models\Group;
use dastiii\Permissions\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use dastiii\Permissions\Models\Permission;
use dastiii\Permissions\Observers\RoleObserver;
use dastiii\Permissions\Observers\GroupObserver;
use dastiii\Permissions\Contracts\Role as RoleContract;
use dastiii\Permissions\Contracts\Group as GroupContract;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

class PermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app->bind(RoleContract::class, Role::class);
        $this->app->bind(GroupContract::class, Group::class);
        $this->app->bind(PermissionContract::class, Permission::class);

        $this->app->make(RoleContract::class)::observe(RoleObserver::class);
        $this->app->make(GroupContract::class)::observe(GroupObserver::class);
        $this->app->make(config('auth.providers.users.model'))::observe(UserObserver::class);
    }
}