<?php

namespace dastiii\Permissions\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use dastiii\Permissions\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use dastiii\Permissions\Contracts\Group as GroupContract;

class Group extends Model implements GroupContract
{
    use HasPermissions;

    /**
     * Mass-assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        "name",
    ];

    /**
     * Boot method.
     */
    protected static function boot() {
        parent::boot();

        static::saved(function($model) {
            Cache::forget($model->getCacheKey());
        });
    }

    /**
     * User relaitonship.
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        $userClass = config('auth.providers.users.model');

        return $this->belongsToMany($userClass);
    }

    /**
     * Returns the group with the given name.
     *
     * @param string $name
     *
     * @return GroupContract
     */
    public function findByName(string $name) : GroupContract
    {
        return static::whereName($name)->first();
    }
}