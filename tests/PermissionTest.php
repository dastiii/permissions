<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class PermissionTest extends TestCase
{
    /** @test */
    public function it_has_a_roles_relationship()
    {
        $this->assertInstanceOf(MorphToMany::class, $this->permission->roles());
        $this->assertInstanceOf(Collection::class, $this->permission->getAttribute('roles'));
    }

    /** @test */
    public function it_has_a_groups_relationship()
    {
        $this->assertInstanceOf(MorphToMany::class, $this->permission->groups());
        $this->assertInstanceOf(Collection::class, $this->permission->getAttribute('groups'));
    }

    /** @test */
    public function it_has_a_users_relationship()
    {
        $this->assertInstanceOf(MorphToMany::class, $this->permission->users());
        $this->assertInstanceOf(Collection::class, $this->permission->getAttribute('users'));
    }
}