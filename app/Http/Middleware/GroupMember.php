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





