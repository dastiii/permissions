<?php

namespace dastiii\Permissions;

use dastiii\Permissions\Models\Role;
use dastiii\Permissions\Models\Group;
use Illuminate\Support\ServiceProvider;
use dastiii\Permissions\Models\Permission;
use dastiii\Permissions\Contracts\Role as RoleContract;
use dastiii\Permissions\Contracts\Group as GroupContract;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

class PermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreatePermissionsTables')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_permissions_tables.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_permissions_tables.php",
            ], 'migrations');
        }

        $this->app->bind(RoleContract::class, Role::class);
        $this->app->bind(GroupContract::class, Group::class);
        $this->app->bind(PermissionContract::class, Permission::class);
    }
}
