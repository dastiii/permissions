<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use dastiii\Permissions\Contracts\Group as GroupContract;

class HasGroupsTest extends TestCase
{
    /**
     * @var GroupContract
     */
    protected $anotherGroup;

    /**
     * @var User
     */
    protected $userWithGroups;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();

        $this->anotherGroup = app(GroupContract::class)->create([
            'name' => 'Another Group'
        ]);

        $this->userWithGroups = User::create([
            'name' => 'John Doe 2',
            'email' => 'john@doe2.com',
            'password' => bcrypt('secret'),
        ]);

        $this->userWithGroups->groups()
            ->attach([$this->group->getAttribute('id'), $this->anotherGroup->getAttribute('id')]);
    }

    /** @test */
    public function it_has_a_groups_relationship()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->user->groups());
        $this->assertInstanceOf(Collection::class, $this->user->getAttribute('groups'));
    }
    
    /** @test */
    public function it_can_add_a_user_to_a_group()
    {
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->group));

        $this->user->addToGroup($this->group);

        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->group));
    }

    /** @test */
    public function it_can_add_a_user_to_a_group_using_the_groups_id()
    {
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->group));

        $this->user->addToGroup($this->group->getAttribute('id'));

        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->group));
    }

    /** @test */
    public function it_can_add_a_user_to_a_group_using_the_groups_name()
    {
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->group));

        $this->user->addToGroup($this->group->getAttribute('name'));

        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->group));
    }

    /** @test */
    public function it_can_remove_a_user_from_a_group()
    {
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->group));

        $this->userWithGroups->removeFromGroup($this->group);

        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->group));
    }

}