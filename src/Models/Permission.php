<?php

namespace dastiii\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use dastiii\Permissions\Contracts\Role as RoleContract;
use dastiii\Permissions\Contracts\Group as GroupContract;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

class Permission extends Model implements PermissionContract
{
    /**
     * Mass-assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'is_backend',
    ];

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users() : MorphToMany
    {
        $userClass = config('auth.providers.users.model');

        return $this
            ->morphedByMany($userClass, 'model', 'model_permission')
            ->withPivot('is_granted');
    }

    /**
     * Role relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function roles() : MorphToMany
    {
        $roleClass = app(RoleContract::class);

        return $this
            ->morphedByMany(get_class($roleClass), 'model', 'model_permission')
            ->withPivot('is_granted');
    }

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function groups() : MorphToMany
    {
        $groupClass = app(GroupContract::class);

        return $this
            ->morphedByMany(get_class($groupClass), 'model', 'model_permission')
            ->withPivot('is_granted');
    }

    /**
     * Returns a permission by its name.
     *
     * @param $name
     *
     * @return PermissionContract
     */
    public function findByName($name): PermissionContract
    {
        return static::whereName($name)->first();
    }
}
