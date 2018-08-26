<?php

namespace dastiii\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Permission
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
}