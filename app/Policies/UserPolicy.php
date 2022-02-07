<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Allow the user to get only their own contacts list
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function viewContactsList(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
