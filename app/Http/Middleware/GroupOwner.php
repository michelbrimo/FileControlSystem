<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Group;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = auth()->user()->id;
        $admin_id = $request->route('group_name')->admin_id;

        if($admin_id == $user_id) return $next($request);

        return response()->json([
            'message' => 'You are not the admin of that group.' 
        ], 403);
    }
}
