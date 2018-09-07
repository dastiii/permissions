<?php

namespace dastiii\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role extends Model
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
    public function findByName(string $name) : self;
}
