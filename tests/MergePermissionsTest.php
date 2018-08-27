<?php

namespace dastiii\Permissions\Test;

use dastiii\Permissions\Models\Permission;
use dastiii\Permissions\Models\Role;

class MergePermissionsTest extends TestCase
{
    protected $anotherRole;

    /**
     * SetUp
     */
    public function setUp()
    {
        parent::setUp();

        $this->anotherRole = Role::create([
            'name' => 'TestRole',
            'weight' => 50,
            'is_default' => false,
        ]);

        $p1 = factory(Permission::class, 5)->create()->each(function ($item) {
            $this->role->grant($item);
        });

        $p2 = factory(Permission::class, 5)->create()->each(function ($item) {
            $this->role->deny($item);
        });

        $p2->each(function ($item) {
            $this->anotherRole->grant($item);
        });

        $p1->each(function ($item) {
            $this->anotherRole->deny($item);
        });
    }

    /** @test */
    public function test_test_test()
    {
        $this->role->grant($this->permission);
        $this->user->attachRoles($this->role, $this->anotherRole);

        $this->user->mergePermissions();

        dump($this->user->mergedPermissions);
    }
}