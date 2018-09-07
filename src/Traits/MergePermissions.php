<?php

namespace dastiii\Permissions\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use dastiii\Permissions\Contracts\Permission as PermissionContract;

trait MergePermissions
{
    use HasGroups, HasRoles, HasPermissions;

    /**
     * @var Collection
     */
    public $mergedPermissions;

    /**
     * @var Collection
     */
    protected $cacheTags;

    /**
     * @var Collection
     */
    protected $rolePermissions;

    /**
     * @var  Collection
     */
    protected $groupPermissions;

    /**
     * Returns the tags used for the users permission cache.
     *
     * @return string
     */
    public function getCacheTagsCacheKey()
    {
        return get_class($this).'.'.$this->id.'.permissions.cacheTags';
    }

    /**
     * Merges the users permissions (including his Role, Group and own permissions).
     *
     * @return void
     */
    public function mergePermissions() : void
    {
        if (Cache::has($this->getCacheTagsCacheKey())) {
            $cacheTags = Cache::get($this->getCacheTagsCacheKey());

            if (Cache::tags($cacheTags)->has($this->getCacheKey())) {
                $this->mergedPermissions = Cache::tags($cacheTags)->get($this->getCacheKey());

                return;
            }
        }

        $this->mergedPermissions = Collection::make();
        $this->cacheTags = Collection::make();

        $this->mergeRolePermissions();
        $this->mergeGroupPermissions();
        $this->mergeUserPermissions();

        Cache::forever($this->getCacheTagsCacheKey(), $this->cacheTags);

        Cache::tags($this->cacheTags->all())
            ->forever($this->getCacheKey(), $this->mergedPermissions);
    }

    /**
     * Merges role permissions.
     *
     * @return void
     */
    protected function mergeRolePermissions() : void
    {
        $this->rolePermissions = Collection::make();

        $this->roles()->orderBy('weight')->get()->each(function ($role) {
            $this->cacheTags->push($role->getCacheKey());

            $this->rolePermissions = $this->rolePermissions->merge(
                $role->permissions()->get()->mapWithKeys(function ($permission) {
                    return $this->transformPermissionToArray($permission);
                })
            );
        });

        $this->mergedPermissions = $this->mergedPermissions->merge($this->rolePermissions);
    }

    /**
     * Merges group permissions.
     *
     * @return void
     */
    protected function mergeGroupPermissions() : void
    {
        $this->groupPermissions = Collection::make();

        $this->groups()->orderBy('id')->get()->each(function ($group) {
            $this->cacheTags->push($group->getCacheKey());

            $this->groupPermissions = $this->groupPermissions->merge(
                $group->permissions()->get()->filter(function ($permission) {
                    if ($this->groupPermissions
                        ->filter(function ($groupPermission) use ($permission) {
                            return $groupPermission['id'] === $permission->id
                                && $groupPermission['isGranted'] === false;
                        })->isNotEmpty()) {
                        return false;
                    }

                    return true;
                })->mapWithKeys(function ($permission) {
                    return $this->transformPermissionToArray($permission);
                })
            );
        });

        $this->mergedPermissions = $this->mergedPermissions->merge($this->groupPermissions);
    }

    /**
     * Merges the user permissions in.
     *
     * @return void
     */
    protected function mergeUserPermissions() : void
    {
        $this->mergedPermissions = $this->mergedPermissions->merge(
            $this->permissions()->get()->mapWithKeys(function ($permission) {
                return $this->transformPermissionToArray($permission);
            })
        );
    }

    /**
     * Transforms the permission instance to an array (for mapWithKeys()).
     *
     * @param PermissionContract $permission
     *
     * @return array
     */
    protected function transformPermissionToArray(PermissionContract $permission)
    {
        return [
            $permission->name => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'isBackend' => (bool) $permission->is_backend,
                'isGranted' => (bool) $permission->pivot->is_granted,
            ],
        ];
    }

    /**
     * Checks if the user has the given permission.
     *
     * @param PermissionContract $permission
     *
     * @return bool
     */
    public function hasAccess(PermissionContract $permission)
    {
        if (is_null($this->mergedPermissions)) {
            $this->mergePermissions();
        }

        return $this->mergedPermissions
            ->whereStrict('id', $permission->id)
            ->whereStrict('isGranted', true)
            ->isNotEmpty();
    }
}
