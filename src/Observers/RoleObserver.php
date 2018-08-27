<?php

namespace dastiii\Permissions\Observers;

use Illuminate\Support\Facades\Cache;
use dastiii\Permissions\Contracts\Role as RoleContract;

class RoleObserver
{
    /**
     * Handle the Role "updated" event.
     *
     * @param  RoleContract $role
     *
     * @return void
     */
    public function saved(RoleContract $role)
    {
        Cache::forget($role->getCacheKey());
    }
}