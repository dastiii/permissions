<?php

namespace dastiii\Permissions\Test;

use Illuminate\Support\Facades\Cache;

class HasPermissionsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_role_permission_is_granted()
    {
        Cache::shouldReceive('tags')
            ->once()
            ->with($this->role->getCacheKey())
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once()
            ->andReturnTrue();

        $this->role->grant($this->permission);
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_role_permission_is_denied()
    {
        Cache::shouldReceive('tags')
            ->once()
            ->with($this->role->getCacheKey())
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once()
            ->andReturnTrue();

        $this->role->deny($this->permission);
    }

    /** @test */
    public function it_can_grant_permissions_to_a_role()
    {
        $this->assertFalse($this->role->hasPermission($this->permission));

        $this->role->grant($this->permission);

        $this->assertTrue($this->role->hasPermission($this->permission));
    }

    /** @test */
    public function it_can_deny_permissions_to_a_role()
    {
        $this->role->grant($this->permission);

        $this->assertTrue($this->role->hasPermission($this->permission));

        $this->role->deny($this->permission);

        $this->assertFalse($this->role->hasPermission($this->permission));
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_group_permission_is_granted()
    {
        Cache::shouldReceive('tags')
            ->once()
            ->with($this->group->getCacheKey())
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once()
            ->andReturnTrue();

        $this->group->grant($this->permission);
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_group_permission_is_denied()
    {
        Cache::shouldReceive('tags')
            ->once()
            ->with($this->group->getCacheKey())
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once()
            ->andReturnTrue();

        $this->group->deny($this->permission);
    }

    /** @test */
    public function it_can_grant_permissions_to_a_group()
    {
        $this->assertFalse($this->group->hasPermission($this->permission));

        $this->group->grant($this->permission);

        $this->assertTrue($this->group->hasPermission($this->permission));
    }

    /** @test */
    public function it_can_deny_permissions_to_a_group()
    {
        $this->group->grant($this->permission);

        $this->assertTrue($this->group->hasPermission($this->permission));

        $this->group->deny($this->permission);

        $this->assertFalse($this->group->hasPermission($this->permission));
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_user_permission_is_granted()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with($this->user->getCacheKey())
            ->andReturnTrue();

        $this->user->grant($this->permission);
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_user_permission_is_denied()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with($this->user->getCacheKey())
            ->andReturnTrue();

        $this->user->deny($this->permission);
    }

    /** @test */
    public function it_can_grant_permissions_to_a_user()
    {
        $this->assertFalse($this->user->hasPermission($this->permission));

        $this->user->grant($this->permission);

        $this->assertTrue($this->user->hasPermission($this->permission));
    }

    /** @test */
    public function it_can_deny_permissions_to_a_user()
    {
        $this->user->grant($this->permission);

        $this->assertTrue($this->user->hasPermission($this->permission));

        $this->user->deny($this->permission);

        $this->assertFalse($this->user->hasPermission($this->permission));
    }
}
