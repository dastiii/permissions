<?php

namespace dastiii\Permissions\Test;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use dastiii\Permissions\Traits\HasRoles;
use dastiii\Permissions\Traits\HasGroups;
use dastiii\Permissions\Traits\HasPermissions;
use dastiii\Permissions\Traits\MergePermissions;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable, Authenticatable;
    use HasRoles, HasGroups, HasPermissions, MergePermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Boot method.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget($model->getCacheKey());
        });
    }
}
