<?php

namespace App\Providers;

use App\Models\File;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use function PHPUnit\Framework\returnSelf;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Route::bind('group_name', function ($value) {
            try{
                request()->route()->setParameter('group_name', $value);
                return Group::where('name', $value)->firstOrFail();
            }catch (\Exception $e) {
                $currentRouteName = request()->route()->getName();

                if ($currentRouteName === 'Files.uploadFiles')
                {
                    $user_id = auth()->user()->id;
                    $group = Group::create([
                                'name' => $value,
                                'admin_id' => $user_id,
                                'numberOfMembers' => 1
                            ]);
                    $group_id = $group->id;
                    UserGroup::create([
                        'user_id' => $user_id,
                        'group_id' => $group_id,
                    ]);
                    return $group;
                }
                return $value;
            }
        });
        
        Route::bind('username', function ($value) {
            request()->route()->setParameter('username', $value);
            return User::where('username', $value)->firstOrFail();
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(5000)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        
    }
}
