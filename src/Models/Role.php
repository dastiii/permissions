<?php

namespace dastiii\Permissions\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use dastiii\Permissions\Traits\HasPermissions;
use dastiii\Permissions\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model implements RoleContract
{
    use HasPermissions;

    /**
     * Mass-assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'weight',
        'is_default',
    ];

    /**
     * Boot method.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::tags($model->getCacheKey())->flush();
        });
    }

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
