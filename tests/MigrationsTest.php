<?php

namespace dastiii\Permissions\Test;

use Illuminate\Support\Facades\Schema;

class MigrationsTest extends TestCase
{
    /** @test */
    public function it_migrates_the_roles()
    {
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('role_user'));
    }

    /** @test */
    public function it_migrates_the_permissions()
    {
        $this->assertTrue(Schema::hasTable('permissions'));
        $this->assertTrue(Schema::hasTable('model_permission'));
    }

    /** @test */
    public function it_migrates_the_groups()
    {
        $this->assertTrue(Schema::hasTable('groups'));
        $this->assertTrue(Schema::hasTable('group_user'));
    }

}