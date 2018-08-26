<?php

namespace dastiii\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Role
{
    /**
     * User relationship.
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany;

    /**
     * Returns the role with the given name.
     *
     * @param string $name
     *
     * @return Role
     */
    public function findByName(string $name) : Role;
}