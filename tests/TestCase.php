<?php

namespace dastiii\Permissions\Test;

use dastiii\Permissions\Models\Role;
use dastiii\Permissions\Models\Group;
use dastiii\Permissions\Models\Permission;
use dastiii\Permissions\PermissionsServiceProvider;
use dastiii\Permissions\Contracts\Role as RoleContract;
use dastiii\Permissions\Contracts\Group as GroupContract;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var Group
     */
    protected $group;

    /**
     * @var Permission
     */
    protected $permission;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Role
     */
    protected $role;

    protected function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../database/factories');

        $this->prepareDatabase();

        $this->user = User::first();
        $this->role = $this->app->make(RoleContract::class)->first();
        $this->group = $this->app->make(GroupContract::class)->first();
        $this->permission = $this->app->make(PermissionContract::class)->first();
        //
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function prepareDatabase()
    {
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
        $this->artisan('migrate', ['--database' => 'sqlite']);

        $this->app->make(User::class)->create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => bcrypt('secret'),
        ]);
        $this->app->make(RoleContract::class)->create(['name' => 'Administrators', 'weight' => 100, 'is_default' => true]);
        $this->app->make(GroupContract::class)->create(['name' => 'Moderators']);
        $this->app->make(PermissionContract::class)->create([
            'name' => 'users.create',
            'human_readable_name' => 'Create Users',
            'is_backend' => false
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            PermissionsServiceProvider::class
        ];
    }
}