<?php

namespace dastiii\Permissions\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

trait HasPermissions
{
    /**
     * @var PermissionContract
     */
    protected $permissionClass;

    /**
     * @var Collection
     */
    protected $permissionCache;

    /**
     * Returns the Permission class from the service container.
     *
     * @return PermissionContract
     */
    public function getPermissionClass()
    {
        if (! $this->permissionClass) {
            $this->permissionClass = app(PermissionContract::class);
        }

        return $this->permissionClass;
    }

    /**
     * Returns the models cacheKey.
     *
     * @return string
     */
    public function getCacheKey() : string
    {
        return get_class($this) . '.permissions.' . $this->id;
    }

    /**
     * Fetches the permission instance based of its id or name.
     *
     * @param integer|string    $permission
     *
     * @return PermissionContract
     */
    public function getPermissionInstance($permission) : PermissionContract
    {
        if (is_string($permission)) {
            return $this->getPermissionClass()->findByName($permission);
        }

        if (is_int($permission)) {
            return $this->getPermissionClass()->find($permission);
        }
    }

    /**
     * Permission relationship.
     *
     * @return MorphToMany
     */
    public function permissions() : MorphToMany
    {
        return $this
            ->morphToMany(get_class($this->getPermissionClass()), 'model', 'model_permission')
            ->withPivot('is_granted', 'resource_id');
    }

    /**
     * Grants a permission to the model.
     *
     * @param PermissionContract|integer|string     $permission
     * @param integer|null                          $resourceId
     *
     * @return void
     */
    public function grant($permission, $resourceId = null) : void
    {
        $this->attachPermissionToModel($permission, true, $resourceId);
    }

    /**
     * Denies a permission to the model.
     *
     * @param PermissionContract|integer|string     $permission
     * @param integer|null                          $resourceId
     *
     * @return void
     */
    public function deny($permission, $resourceId = null) : void
    {
        $this->attachPermissionToModel($permission, false, $resourceId);
    }

    /**
     * @param PermissionContract $permission
     * @param integer|null $resourceId
     *
     * @return bool
     */
    public function hasPermission($permission, $resourceId = null) : bool
    {
        $this->checkForCachedPermissions();

        $permission = $permission instanceof PermissionContract
            ? $permission : $this->getPermissionInstance($permission);

        return $this->permissionCache
            ->where('id', $permission->id)
            ->filter(function ($value) use ($resourceId) {
                return $value->pivot->resource_id == $resourceId && $value->pivot->is_granted;
            })->count() > 0;
    }

    /**
     * Attaches the permission to the model or updates the pivot data.
     *
     * @param PermissionContract|integer|string     $permission
     * @param bool                                  $isGranted
     * @param integer|null                          $resourceId
     *
     * @return void
     */
    protected function attachPermissionToModel($permission, bool $isGranted, $resourceId = null) : void
    {
        $permission = $permission instanceof PermissionContract
            ? $permission : $this->getPermissionInstance($permission);

        if ($this->permissionPivotExists($permission, $resourceId)) {
            $this->updatePermission($permission, $isGranted, $resourceId);

            return;
        }

        $this->addPermission($permission, $isGranted, $resourceId);
    }

    /**
     * Checks if a pivot already exists
     *
     * @param  PermissionContract   $permission
     * @param  integer|null         $resourceId
     *
     * @return boolean
     */
    protected function permissionPivotExists(PermissionContract $permission, $resourceId) : bool
    {
        return $this->permissions()
            ->where('id', $permission->id)
            ->wherePivot('resource_id', $resourceId)
            ->count() ? true : false;
    }

    /**
     * Adds the permission to the entity
     *
     * @param PermissionContract    $permission
     * @param boolean               $isGranted
     * @param integer|null          $resourceId
     *
     * @return void
     */
    protected function addPermission(PermissionContract $permission, bool $isGranted, $resourceId) : void
    {
        $this->touch();

        $this->permissions()
            ->save($permission, [
                'is_granted' => $isGranted,
                'resource_id' => $resourceId,
            ]);
    }

    /**
     * Updates the permission of the entity
     *
     * @param PermissionContract    $permission
     * @param boolean               $isGranted
     * @param integer|null          $resourceId
     *
     * @return void
     */
    protected function updatePermission(PermissionContract $permission, bool $isGranted, $resourceId) : void
    {
        $this->touch();

        $this->permissions()
            ->wherePivot('resource_id', $resourceId)
            ->updateExistingPivot($permission->id, [
                'is_granted' => $isGranted,
            ]);
    }

    /**
     * Retrieve permissions from cache or load permissions and cache them.
     *
     * @return void
     */
    protected function checkForCachedPermissions() : void
    {
        if (Cache::has($this->getCacheKey())) {
            $this->permissionCache = Cache::get($this->getCacheKey());

            return;
        }

        Cache::forever(
            $this->getCacheKey(),
            $this->permissionCache = $this->permissions()->get()
        );
    }

    /**
     * Flushes the permissions cache.
     *
     * @return void
     */
    protected function flushCache() : void
    {
        Cache::forget($this->getCacheKey());
    }
}
