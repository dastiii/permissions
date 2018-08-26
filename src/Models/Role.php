<?php

namespace dastiii\Permissions\Models;

use dastiii\Permissions\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;
use dastiii\Permissions\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

class Role extends Model implements RoleContract
{
    use HasPermissions;

    /**
     * Mass-assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "weight",
        "is_default",
    ];

    /**
     * User relationship.
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        $userClass = config('auth.providers.users.model');

        return $this->belongsToMany($userClass);
    }

    /**
     * Returns the role with the given name.
     *
     * @param string $name
     *
     * @return RoleContract
     */
    public function findByName(string $name) : RoleContract
    {
        return self::whereName($name)->first();
    }
}