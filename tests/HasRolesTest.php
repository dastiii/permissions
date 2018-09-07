<?php

namespace dastiii\Permissions\Test;

use Illuminate\Database\Eloquent\Collection;
use dastiii\Permissions\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HasRolesTest extends TestCase
{
    /**
     * @var RoleContract
     */
    protected $anotherRole;

    /**
     * @var User
     */
    protected $userWithRoles;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();

        $this->anotherRole = app(RoleContract::class)->create([
            'name' => 'TestRole',
            'weight' => 42,
            'is_default' => false,
        ]);

        $this->userWithRoles = User::create([
            'name' => 'John Doe 2',
            'email' => 'john@doe2.com',
            'password' => bcrypt('secret'),
        ]);

        $this->userWithRoles
            ->roles()
            ->attach([
                $this->role->getAttribute('id'),
                $this->anotherRole->getAttribute('id'),
            ]);
    }

    /** @test */
    public function it_has_a_roles_relationship()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->user->roles());
        $this->assertInstanceOf(Collection::class, $this->user->getAttribute('roles'));
    }

    /** @test */
    public function it_can_attach_a_role_to_a_user_using_role_instance()
    {
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->role));

        $this->user->attachRole($this->role);

        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->role));
    }

    /** @test */
    public function it_can_attach_a_role_to_a_user_using_only_the_role_id()
    {
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->role));

        $this->user->attachRole($this->role->getAttribute('id'));

        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->role));
    }

    /** @test */
    public function it_can_attach_a_role_to_a_user_using_only_the_role_name()
    {
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->role));

        $this->user->attachRole($this->role->getAttribute('name'));

        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->role));
    }

    /** @test */
    public function it_can_attach_multiple_roles_to_a_user_at_once()
    {
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->role));
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->anotherRole));

        $this->user->attachRoles($this->role, $this->anotherRole);

        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->role));
        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->anotherRole));
    }

    /** @test */
    public function it_can_attach_multiple_roles_to_a_user_at_once_using_an_array()
    {
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->role));
        $this->assertFalse($this->user->getAttribute('roles')->contains($this->anotherRole));

        $this->user->attachRoles([$this->role, $this->anotherRole]);

        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->role));
        $this->assertTrue($this->user->fresh()->getAttribute('roles')->contains($this->anotherRole));
    }

    /** @test */
    public function it_can_detach_a_role_from_a_user_using_the_role_instance()
    {
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->role));

        $this->userWithRoles->detachRole($this->role);

        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->role));
    }

    /** @test */
    public function it_can_detach_a_role_from_a_user_using_only_the_roles_id()
    {
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->role));

        $this->userWithRoles->detachRole($this->role->getAttribute('id'));

        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->role));
    }

    /** @test */
    public function it_can_detach_a_role_from_a_user_using_only_the_roles_name()
    {
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->role));

        $this->userWithRoles->detachRole($this->role->getAttribute('name'));

        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->role));
    }

    /** @test */
    public function it_can_detach_multiple_roles_from_a_user_at_once()
    {
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->role));
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->anotherRole));

        $this->userWithRoles->detachRoles($this->role, $this->anotherRole);

        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->role));
        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->anotherRole));
    }

    /** @test */
    public function it_can_detach_multiple_roles_from_a_user_at_once_using_an_array()
    {
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->role));
        $this->assertTrue($this->userWithRoles->getAttribute('roles')->contains($this->anotherRole));

        $this->userWithRoles->detachRoles([$this->role, $this->anotherRole]);

        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->role));
        $this->assertFalse($this->userWithRoles->fresh()->getAttribute('roles')->contains($this->anotherRole));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_given_role()
    {
        $this->assertFalse($this->user->hasRole($this->role));

        $this->assertTrue($this->userWithRoles->hasRole($this->role));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_role_with_the_given_id()
    {
        $this->assertFalse($this->user->hasRole($this->role->getAttribute('id')));

        $this->assertTrue($this->userWithRoles->hasRole($this->role->getAttribute('id')));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_role_with_the_given_name()
    {
        $this->assertFalse($this->user->hasRole($this->role->getAttribute('name')));

        $this->assertTrue($this->userWithRoles->hasRole($this->role->getAttribute('name')));
    }
}
