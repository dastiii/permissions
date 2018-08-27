<?php

namespace dastiii\Permissions\Observers;

use Illuminate\Support\Facades\Cache;
use dastiii\Permissions\Contracts\Group as GroupContract;

class GroupObserver
{
    /**
     * Handle the Group "updated" event.
     *
     * @param  GroupContract $group
     *
     * @return void
     */
    public function saved(GroupContract $group)
    {
        Cache::forget($group->getCacheKey());
    }
}