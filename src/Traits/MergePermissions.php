<?php

namespace dastiii\Permissions\Traits;

use Illuminate\Support\Collection;

trait MergePermissions
{
    /**
     * @var Collection
     */
    protected $mergedPermissions;

    protected function mergePermissions()
    {
        // Roles.
        $this->roles()->orderBy('weight')->get()
            ->each(function ($item) {
                $item->permissions()->get()
                    ->each(function ($item) {
                        $this->mergeRolePermissionIn($item, true);
                    });
            });

        // Groups.

        // User.
    }
//
//    protected function mergeRolePermissionIn(PermissionContract $permission)
//    {
//        if ($this->mergedPermissions->)
//    }
}