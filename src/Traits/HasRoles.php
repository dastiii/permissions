<?php

namespace dastiii\Permissions\Traits;

use dastiii\Permissions\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * @var RoleContract
     */
    protected $roleClass;

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    abstract protected function belongsToMany(
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null
    );

    /**
     * Returns the Role class from the service container.
     *
     * @return RoleContract|\Illuminate\Foundation\Application|mixed
     */
    public function getRoleClass() : RoleContract
    {
        if (! $this->roleClass) {
            $this->roleClass = app(RoleContract::class);
        }

        return $this->roleClass;
    }

    /**
     * Role relationship.
     *
     * @return mixed
     */
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(get_class($this->getRoleClass()));
    }

    /**
     * Attach a role to a user.
     *
     * @param RoleContract|int|string $role
     *
     * @return void
     */
    public function attachRole($role) : void
    {
        if ($role instanceof RoleContract) {
            $this->roles()->attach($role->getAttribute('id'));

            return;
        }

        if (is_int($role)) {
            $this->roles()->attach($role);

            return;
        }

        $this->roles()->attach(
            $this->getRoleClass()->findByName($role)->getAttribute('id')
        );
    }

    /**
     * Attach multiple roles to a user.
     *
     * @param mixed ...$roles
     *
     * return void
     */
    public function attachRoles(...$roles) : void
    {
        if (is_array($roles[0])) {
            foreach ($roles[0] as $role) {
                $this->attachRole($role);
            }

            return;
        }

        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    /**
     * Detaches a role from a user.
     *
     * @param RoleContract|int|string $role
     *
     * @return void
     */
    public function detachRole($role) : void
    {
        if ($role instanceof RoleContract) {
            $this->roles()->detach($role->getAttribute('id'));

            return;
        }

        if (is_int($role)) {
            $this->roles()->detach($role);

            return;
        }

        $this->roles()->detach(
            $this->getRoleClass()->findByName($role)->getAttribute('id')
        );
    }

    /**
     * Detaches multiple roles from a user.
     *
     * @param mixed ...$roles
     *
     * @return void
     */
    public function detachRoles(...$roles) : void
    {
        if (is_array($roles[0])) {
            foreach ($roles[0] as $role) {
                $this->detachRole($role);
            }

            return;
        }

        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }

    /**
     * Checks if the user has the given role.
     *
     * @param RoleContract|int|string $role
     *
     * @return bool
     */
    public function hasRole($role) : bool
    {
        if ($role instanceof RoleContract) {
            return $this->getAttribute('roles')->contains($role);
        }

        if (is_int($role)) {
            return in_array($role, array_values($this->getAttribute('roles')->pluck('id')->all()));
        }

        return in_array($role, array_values($this->getAttribute('roles')->pluck('name')->all()));
    }
}
