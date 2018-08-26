<?php

namespace dastiii\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Group
{
    /**
     * Permission relationship.
     *
     * @return MorphToMany
     */
    public function permissions() : MorphToMany;

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
    public function findByName(string $name) : Group;
}