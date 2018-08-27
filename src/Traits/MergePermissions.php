<?php

namespace dastiii\Permissions\Traits;

use Illuminate\Support\Collection;

trait MergePermissions
{
    /**
     * @var Collection
     */
    public $mergedPermissions;

    public function mergePermissions()
    {
        $this->mergedPermissions = collect();

        // Roles.
        $this->roles()->orderBy('weight')->get()
            ->each(function ($item) {
                $this->testMerge($item->permissions()->get());
//                    ->each(function ($item) {
//                        $this->mergeRolePermissionIn($item, true);
//                    });
            });

        // Groups.

        // User.
    }

    protected function testMerge($collection)
    {
        $this->mergedPermissions = $this->mergedPermissions->merge($collection);
    }

//    protected function mergeRolePermissionIn(PermissionContract $permission)
//    {
//    }
}