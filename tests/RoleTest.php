<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleTest extends TestCase
{
    /** @test */
    public function it_has_a_permissions_relationship()
    {
        $this->assertInstanceOf(MorphToMany::class, $this->role->permissions());
        $this->assertInstanceOf(Collection::class, $this->role->getAttribute('permissions'));
    }

    /** @test */
    public function it_has_a_user_relationship()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->role->users());
        $this->assertInstanceOf(Collection::class, $this->role->getAttribute('users'));
    }
}
