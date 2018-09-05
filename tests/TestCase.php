<?php

namespace dastiii\Permissions\Test;

use dastiii\Permissions\Models\Role;
use dastiii\Permissions\Models\Group;
use dastiii\Permissions\Models\Permission;
use dastiii\Permissions\PermissionsServiceProvider;

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

        $this->user = create(User::class);
        $this->role = create(Role::class);
        $this->group = create(Group::class);
        $this->permission = create(Permission::class);
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

        include_once __DIR__.'/../database/migrations/create_permissions_tables.php.stub';
        (new \CreatePermissionsTables())->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            PermissionsServiceProvider::class
        ];
    }
}
