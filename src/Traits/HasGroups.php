<?php

namespace dastiii\Permissions\Traits;

use dastiii\Permissions\Contracts\Group as GroupContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasGroups
{
    /**
     * @var GroupContract
     */
    protected $groupClass;

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    abstract protected function belongsToMany(
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null
    );

    /**
     * Returns the group class from the service container.
     *
     * @return GroupContract
     */
    protected function getGroupClass() : GroupContract
    {
        if (! $this->groupClass) {
            $this->groupClass = app(GroupContract::class);
        }

        return $this->groupClass;
    }

    /**
     * Group relationship.
     *
     * @return mixed
     */
    public function groups() : BelongsToMany
    {
        return $this->belongsToMany(get_class($this->getGroupClass()));
    }

    /**
     * Adds a group to a user.
     *
     * @param GroupContract|int|string $group
     *
     * @return void
     */
    public function addToGroup($group) : void
    {
        $this->groups()->attach(
            is_string($group) ? $this->getGroupClass()->findByName($group) : $group
        );
    }

    /**
     * Adds multiple groups to a user.
     *
     * @param mixed ...$groups
     */
    public function addToGroups(...$groups) : void
    {
        if (is_array($groups[0])) {
            foreach ($groups[0] as $group) {
                $this->addToGroup($group);
            }

            return;
        }

        foreach ($groups as $group) {
            $this->addToGroup($group);
        }
    }

    /**
     * Removes a user from a group.
     *
     * @param GroupContract|int|string $group
     *
     * @return void
     */
    public function removeFromGroup($group) : void
    {
        $this->groups()->detach(
            is_string($group) ? $this->getGroupClass()->findByName($group) : $group
        );
    }

    /**
     * Removes multiple groups from a user.
     *
     * @param mixed ...$groups
     *
     * @return void
     */
    public function removeFromGroups(...$groups) : void
    {
        if (is_array($groups[0])) {
            foreach ($groups[0] as $group) {
                $this->removeFromGroup($group);
            }

            return;
        }

        foreach ($groups as $group) {
            $this->removeFromGroup($group);
        }
    }
}
