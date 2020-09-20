<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //判断用户id和博客的用户id一样才可以删除
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
}
