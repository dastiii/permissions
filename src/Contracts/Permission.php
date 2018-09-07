<?php

namespace dastiii\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Permission extends Model
{
    /**
     * Role relationship.
     *
     * @return MorphToMany
     */
    public function roles() : MorphToMany;

    /**
     * Group relationship.
     *
     * @return MorphToMany
     */
    public function groups() : MorphToMany;

    /**
     * User relationship.
     *
     * @return MorphToMany
     */
    public function users() : MorphToMany;

    /**
     * Returns the permission by its name.
     *
     * @param $name
     *
     * @return Permission
     */
    public function findByName($name) : self;
}
