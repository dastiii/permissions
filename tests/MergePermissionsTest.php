<?php

namespace dastiii\Permissions\Test;

use Illuminate\Support\Collection;
use dastiii\Permissions\Models\Role;
use dastiii\Permissions\Models\Group;
use Illuminate\Support\Facades\Cache;
use dastiii\Permissions\Models\Permission;

class MergePermissionsTest extends TestCase
{
    /**
     * SetUp.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_cache_merged_permissions()
    {
        $role = create(Role::class);
        $role->grant($permissionA = create(Permission::class));
        $role->deny($permissionB = create(Permission::class));

        $this->user->attachRole($role);

        Cache::shouldReceive('has')
            ->once()
            ->with($this->user->getCacheTagsCacheKey())
            ->andReturnFalse();

        Cache::shouldReceive('forever')
            ->once()
            ->with($this->user->getCacheTagsCacheKey(), Collection::class);

        Cache::shouldReceive('tags')
            ->once()
            ->with([$role->getCacheKey()])
            ->andReturnSelf();

        Cache::shouldReceive('forever')
            ->once()
            ->with($this->user->getCacheKey(), Collection::class);

        $this->user->hasAccess($permissionA);
    }

    /** @test */
    public function it_should_retrieve_permissions_from_cache()
    {
        $role = create(Role::class);
        $role->grant($permissionA = create(Permission::class));
        $role->deny($permissionB = create(Permission::class));

        $this->user->attachRole($role);

        Cache::shouldReceive('has')
            ->once()
            ->with($this->user->getCacheTagsCacheKey())
            ->andReturnTrue();

        Cache::shouldReceive('get')
            ->once()
            ->with($this->user->getCacheTagsCacheKey())
            ->andReturn([$role->getCacheKey()]);

        Cache::shouldReceive('tags')
            ->once()
            ->with([$role->getCacheKey()])
            ->andReturnSelf();

        Cache::shouldReceive('has')
            ->once()
            ->with($this->user->getCacheKey())
            ->andReturnTrue();

        Cache::shouldReceive('tags')
            ->once()
            ->with([$role->getCacheKey()])
            ->andReturnSelf();

        Cache::shouldReceive('get')
            ->once()
            ->with($this->user->getCacheKey())
            ->andReturn(collect([
                $permissionA->name => [
                    'id' => $permissionA->id,
                    'name' => $permissionA->name,
                    'isBackend' => $permissionA->is_backend,
                    'isGranted' => true,
                ],

                $permissionB->name => [
                    'id' => $permissionB->id,
                    'name' => $permissionB->name,
                    'isBackend' => $permissionB->is_backend,
                    'isGranted' => false,
                ],
            ]));

        $this->user->hasAccess($permissionB);
    }

    /** @test */
    public function it_should_deny_access_when_permission_is_neither_granted_nor_denied()
    {
        $permission = create(Permission::class);
        $user = create(User::class);

        $this->assertFalse($user->hasAccess($permission));
    }

    /** @test */
    public function it_should_override_roles_with_lower_weight_when_merging_role_permissions()
    {
        $permission = create(Permission::class);
        $roleWithLowerWeight = create(Role::class, ['weight' => 1]);
        $roleWithHigherWeight = create(Role::class, ['weight' => 2]);

        $user = create(User::class);
        $user->attachRoles($roleWithLowerWeight, $roleWithHigherWeight);

        $roleWithLowerWeight->deny($permission);
        $roleWithHigherWeight->grant($permission);

        $this->assertTrue($user->hasAccess($permission));
    }

    /** @test */
    public function it_should_merge_the_role_permissions()
    {
        $permissionA = create(Permission::class, ['display_name' => 'Permission A']);
        $permissionB = create(Permission::class, ['display_name' => 'Permission B']);
        $permissionC = create(Permission::class, ['display_name' => 'Permission C']);
        $permissionD = create(Permission::class, ['display_name' => 'Permission D']);

        $roleA = create(Role::class, ['weight' => 1]);
        $roleB = create(Role::class, ['weight' => 2]);

        $roleA->grant($permissionA);
        $roleA->grant($permissionB);
        $roleB->deny($permissionA);
        $roleB->grant($permissionC);

        $this->user->attachRoles($roleA, $roleB);

        $this->assertTrue($this->user->hasAccess($permissionC));
        $this->assertTrue($this->user->hasAccess($permissionB));
        $this->assertFalse($this->user->hasAccess($permissionA));
        $this->assertFalse($this->user->hasAccess($permissionD));
    }

    /** @test */
    public function it_should_deny_a_group_permission_when_any_group_denies_it_although_it_may_be_granted_through_a_different_group()
    {
        $permission = create(Permission::class);
        $groupA = create(Group::class);
        $groupB = create(Group::class);
        $groupC = create(Group::class);

        $groupA->grant($permission);
        $groupB->deny($permission);
        $groupC->grant($permission);

        $user = create(User::class);
        $user->addToGroups($groupA, $groupB);

        $this->assertFalse($user->hasAccess($permission));
    }

    /** @test */
    public function it_should_override_roles_with_group_permissions()
    {
        $permissionA = create(Permission::class, ['display_name' => 'Permission A (denied!)']);
        $permissionB = create(Permission::class, ['display_name' => 'Permission B (denied!)']);
        $permissionC = create(Permission::class, ['display_name' => 'Permission C (granted!)']);
        $permissionD = create(Permission::class, ['display_name' => 'Permission D (granted!)']);

        $role = create(Role::class);

        $role->grant($permissionA);
        $role->grant($permissionB);
        $role->grant($permissionC);
        $role->deny($permissionD);

        $this->user->attachRole($role);

        $groupA = create(Group::class);
        $groupB = create(Group::class);

        $groupA->deny($permissionB);
        $groupA->deny($permissionA);
        $groupB->grant($permissionD);
        $groupB->grant($permissionB);

        $this->user->addToGroups($groupA, $groupB);

        $this->assertFalse($this->user->hasAccess($permissionA));
        $this->assertFalse($this->user->hasAccess($permissionB));
        $this->assertTrue($this->user->hasAccess($permissionC));
        $this->assertTrue($this->user->hasAccess($permissionD));
    }

    /** @test */
    public function it_should_override_permissions_when_attached_directly_to_the_user()
    {
        $permissionA = create(Permission::class);
        $permissionB = create(Permission::class);

        $role = create(Role::class);
        $role->grant($permissionA);

        $group = create(Group::class);
        $group->deny($permissionB);

        $user = create(User::class);
        $user->attachRole($role);
        $user->addToGroup($group);

        $user->grant($permissionB);
        $user->deny($permissionA);

        $this->assertTrue($user->hasAccess($permissionB));
        $this->assertFalse($user->hasAccess($permissionA));
    }
}
