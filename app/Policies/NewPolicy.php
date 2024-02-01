<?php

namespace App\Policies;

use App\Models\User;

class NewPolicy
{
    public function manageNews(User $user)
    {
        return $user->isAdmin();
    }
}
