<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasAdminAccess() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if (!$user->hasAdminAccess()) {
            return false;
        }

        if ($model->role === 'owner' && $user->role !== 'owner') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->hasAdminAccess()) {
            return false;
        }

        if ($user->id === $model->id) {
            return false;
        }

        if ($model->role === 'owner') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can reset the password of the model.
     */
    public function resetPassword(User $user, User $model): bool
    {
        return $user->hasAdminAccess() && 
               $user->id !== $model->id;
    }

    /**
     * Determine whether the user can toggle the status of the model.
     */
    public function toggleStatus(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
