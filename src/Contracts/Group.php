<?php

namespace dastiii\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Group extends Model
{
    /**
     * User relationship.
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany;

    /**
     * Returns the group with the given name.
     *
     * @param string $name
     *
     * @return Group
     */
    public function findByName(string $name) : self;
}
