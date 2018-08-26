<?php

namespace dastiii\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use dastiii\Permissions\Contracts\Group as GroupContract;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

class Group extends Model implements GroupContract
{
    /**
     * Mass-assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        "name",
    ];

    /**
     * Permission relationship.
     *
     * @return MorphToMany
     */
    public function permissions() : MorphToMany
    {
        $permissionClass = app(PermissionContract::class);

        return $this
            ->morphToMany(get_class($permissionClass), 'model', 'model_permission')
            ->withPivot('state', 'resource_id');
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