<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    # replaced with middle ware
    // public function groupAdminPermissions(User $user, $original)
    // {
    //     // return $user->id === $group->admin_id
    //     // ? Response::allow()
    //     // : Response::denyWithStatus(false , 'You do not own this group.', 403);
    // }
}
