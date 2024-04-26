<?php

namespace App\Http\Middleware;

use App\Services\Backend\UserLogService;
use Closure;
use Illuminate\Http\Request;

class FinishAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('users')->user() && !optional(auth('users')->user())->profile) {
            (new UserLogService)->addLog(auth('users')->user(),null,false);
            return to_route('login.first')->with('success', '首次登录需要您完善个人信息');
        }
        $permission = collect(config('permission'))->map(function ($item) {
            $result = collect([
                ['code' => $item['code'], 'name' => $item['name']]
            ]);
            if (array_key_exists('children', $item)) {
                $result->concat($item['children']);
            }
            return $result->toArray();
        })->collapse()->pluck('code')->toArray();
        $routeName = $request->route()->getName();
        if (in_array($routeName, $permission)) {
            if (!auth('users')->user()->checkPermission($request->route()->getName())) {
                if ($request->ajax()) {
                    (new UserLogService)->addLog(auth('users')->user(),null,false);
                    return response()->json([
                        'code' => 403,
                        'message' => '您无权对此进行操作'
                    ]);
                } else {
                    (new UserLogService)->addLog(auth('users')->user(),null,false);
                    return to_route('error')->with('error', '您无权对此进行操作');
                }
            }
        }
        (new UserLogService)->addLog(auth('users')->user());
        return $next($request);
    }
}