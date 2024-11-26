<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = auth()->user()->id;
        $group_name = $request->route('group_name');

        $group = Group::where('name', $group_name)->first();
        if (!$group) {
            $group = Group::create([
                'name' => $group_name,
                'admin_id' => $user_id
            ]);
            $group_id = $group->id;
            UserGroup::where('user_id', '=', $user_id)
                    ->where('group_id', '=', $group_id)
                    ->first();
            return $next($request);
        }
        
        $group_id = $request->route('group_name')->id;

        $is_member = UserGroup::where('user_id', '=', $user_id)
                              ->where('group_id', '=', $group_id)
                              ->first();
        
        if($is_member) return $next($request);

        return response()->json([
            'message' => 'You are not a member in this group.' 
        ], 403);
    }
}






//         $is_member = UserGroup::where('user_id', '=', $user_id)
//                               ->where('group_id', '=', $group_id)
//                               ->first();

//         if ($is_member) {
//             // If the user is a member, proceed to the next middleware or controller
//             return $next($request);
//         }

//         // If the user is not a member, you can either add them as a member or return a 403
//         // For example, let's add them to the group:
//         UserGroup::create([
//             'user_id' => $user_id,
//             'group_id' => $group_id,
//         ]);

//         // Proceed to the next request
//         return $next($request);