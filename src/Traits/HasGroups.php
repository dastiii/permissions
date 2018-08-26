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

    public function addToGroup($group) : void
    {
        if ($group instanceof GroupContract) {
            $this->groups()->attach($group->id);

            return;
        }

        if (is_int($group)) {
            $this->groups()->attach($group);

            return;
        }

        $this->groups()->attach(
            $this->getGroupClass()->findByName($group)->getAttribute('id')
        );

        return;
    }

    public function removeFromGroup($group) : void
    {
        $this->groups()->detach($group);

        return;
    }
}