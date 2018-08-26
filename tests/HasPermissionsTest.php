<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Mockery\Mock;

class HasPermissionsTest extends TestCase
{
    /** @test */
    public function it_should_cache_permissions()
    {
        Cache::shouldReceive('has')
            ->once()
            ->with($this->role->getCacheKey())
            ->andReturnFalse();

        Cache::shouldReceive('forever')
            ->once()
            ->with($this->role->getCacheKey(), Collection::class)
            ->andReturnTrue();

        $this->role->hasPermission($this->permission);
    }

    /** @test */
    public function it_should_use_cached_permissions()
    {
        $this->role->grant($this->permission);

        Cache::shouldReceive('has')
            ->once()
            ->with($this->role->getCacheKey())
            ->andReturnTrue();

        Cache::shouldReceive('get')
            ->once()
            ->with($this->role->getCacheKey())
            ->andReturn(collect([$this->role->permissions()->first()]));

        $this->role->hasPermission($this->permission);
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_permission_is_granted()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with($this->role->getCacheKey())
            ->andReturnTrue();

        $this->role->grant($this->permission);
    }

    /** @test */
    public function it_should_flush_the_cache_when_a_permission_is_denied()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with($this->role->getCacheKey())
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

}