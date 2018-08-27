<?php

namespace dastiii\Permissions\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param  AuthenticatableContract $user
     *
     * @return void
     */
    public function saved(AuthenticatableContract $user)
    {
        Cache::forget($user->getCacheKey());
    }
}