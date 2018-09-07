<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use dastiii\Permissions\Contracts\Group as GroupContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();

        $this->anotherGroup = app(GroupContract::class)->create([
            'name' => 'Another Group',
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
    public function it_can_add_multiple_users_to_a_group_at_once()
    {
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->group));
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->anotherGroup));

        $this->user->addToGroups($this->group, $this->anotherGroup);

        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->group));
        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->anotherGroup));
    }

    /** @test */
    public function it_can_add_multiple_users_to_a_group_at_once_as_array()
    {
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->group));
        $this->assertFalse($this->user->getAttribute('groups')->contains($this->anotherGroup));

        $this->user->addToGroups([$this->group, $this->anotherGroup]);

        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->group));
        $this->assertTrue($this->user->fresh()->getAttribute('groups')->contains($this->anotherGroup));
    }

    /** @test */
    public function it_can_remove_a_user_from_a_group_using_the_group_instance()
    {
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->group));

        $this->userWithGroups->removeFromGroup($this->group);

        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->group));
    }

    /** @test */
    public function it_can_remove_a_user_from_a_group_using_the_groups_id()
    {
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->group));

        $this->userWithGroups->removeFromGroup($this->group->getAttribute('id'));

        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->group));
    }

    /** @test */
    public function it_can_remove_a_user_from_a_group_using_the_groups_name()
    {
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->group));

        $this->userWithGroups->removeFromGroup($this->group->getAttribute('name'));

        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->group));
    }

    /** @test */
    public function it_can_remove_multiple_groups_from_a_user_at_once()
    {
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->group));
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->anotherGroup));

        $this->userWithGroups->removeFromGroups($this->group, $this->anotherGroup);

        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->group));
        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->anotherGroup));
    }

    /** @test */
    public function it_can_remove_multiple_groups_from_a_user_at_once_using_an_array()
    {
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->group));
        $this->assertTrue($this->userWithGroups->getAttribute('groups')->contains($this->anotherGroup));

        $this->userWithGroups->removeFromGroups([$this->group, $this->anotherGroup]);

        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->group));
        $this->assertFalse($this->userWithGroups->fresh()->getAttribute('groups')->contains($this->anotherGroup));
    }
}
