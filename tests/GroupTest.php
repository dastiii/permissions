<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use dastiii\Permissions\Contracts\Group as GroupContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupTest extends TestCase
{
    /** @test */
    public function it_has_a_permissions_relationship()
    {
        $this->assertInstanceOf(MorphToMany::class, $this->group->permissions());
        $this->assertInstanceOf(Collection::class, $this->group->getAttribute('permissions'));
    }

    /** @test */
    public function it_has_a_user_relationship()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->group->users());
        $this->assertInstanceOf(Collection::class, $this->group->getAttribute('users'));
    }

    /** @test */
    public function it_can_be_found_by_its_name()
    {
        $this->assertTrue(
            app(GroupContract::class)->findByName($this->group->getAttribute('name'))->is($this->group)
        );
    }
}
