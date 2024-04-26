<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $uri = Route::currentRouteName();
        $menus = config('menu');
        $route = function () use ($menus) {
            return self::covertMenu(auth('users')->user(), $menus);
        };
        $title = function () use ($uri, $menus) {
            return $this->searchTitle($uri, $menus);
        };
        $shared = parent::share($request);
        $histories = function () use ($uri, $menus, $shared, $title) {
            return $this->covertHistory($uri, $menus, $shared, $title);
        };
        $children = function () use ($uri, $menus) {
            return $this->covertChildren(auth('users')->user(), $uri, $menus);
        };
        $parentCurrent = function () use ($uri, $menus) {
            return optional(collect($menus)->filter(function ($item) use ($uri) {
                if (!array_key_exists('path', $item))
                    return false;
                if ($item['path'] == $uri)
                    return true;
                if (array_key_exists('children', $item)) {
                    return !empty (collect($item['children'])->where('path', $uri)->first());
                }
                return false;
            })->first())['path'];
        };
        return array_merge($shared, [
            'current' => $uri,
            'parentCurrent' => $parentCurrent,
            'routes' => $route,
            'title' => $title,
            'histories' => $histories,
            'children' => $children,
            'user' => optional($request->user())->only([
                'id',
                'profile',
                'username',
                'mobile',
                'number',
                'email',
                'is_super'
            ]),
            'extra' => function () use ($request) {
                return $request->session()->get('extra', null);
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('errors') ? $request->session()->get('errors')->getBag('default')->getMessages() : null
                ];
            }
        ]);
    }

    /**
     * 获取菜单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  mixed|User $user
     * @param  array  $list
     * @return array
     */
    public static function covertMenu($user, array $list)
    {
        $menus = [];
        $permissions = [];
        if (!$user)
            return $menus;
        if ($user->is_super) {
            $permissions[] = '*';
        } else {
            $rolePermissions = $user->roles->pluck('permission');
            $permissions = array_unique($rolePermissions->flatten()->all());
        }
        $menus = collect($list)->filter(function ($item) use ($permissions) {
            return (in_array('*', $permissions) || in_array($item['path'], $permissions)) && (array_key_exists('show', $item) && $item['show']);
        })->map(function ($item) use ($user) {
            return [
                'name' => $item['name'],
                'path' => $item['path'],
                'intro' => array_key_exists('intro', $item) ? $item['intro'] : null,
                'icon' => array_key_exists('icon', $item) ? $item['icon'] : null,
                'children' => array_key_exists('children', $item) ? self::covertMenu($user, $item['children']) : null,
            ];
        })->values()->toArray();
        return $menus;
    }

    /**
     * 获取标题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string|null $str
     * @param  array       $list
     * @return string
     */
    private function searchTitle(?string $str, array $list)
    {
        $current = env('APP_NAME');
        if (!$str)
            return $current;
        foreach ($list as $rs) {
            if ($rs['path'] == $str) {
                $current = $rs['name'];
                if (array_key_exists('children', $rs) && $rs['children']) {
                    $childName = $this->searchTitle($str, $rs['children']);
                    if ($childName) {
                        $current = $childName;
                    }
                }
                break;
            }
            if (array_key_exists('children', $rs) && $rs['children']) {
                $childName = $this->searchTitle($str, $rs['children']);
                if ($childName != $current) {
                    $current = $childName;
                    break;
                }
            }
        }
        return $current;
    }

    /**
     * 获取面包屑
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string               $uri
     * @param  array                $menus
     * @param  array                $shared
     * @param  string|\Closure|null $title
     * @return array
     */
    private function covertHistory(string $uri, array $menus, array $shared, string|\Closure $title = null): array
    {
        $histories = [
            [
                'name' => '控制台',
                'path' => 'dashboard'
            ]
        ];
        $find = collect($menus)->filter(function ($item) use ($uri) {
            return $item['path'] == $uri || (array_key_exists('children', $item) && collect($item['children'])->filter(fn($_item) => $_item['path'] == $uri)->count());
        })->first();
        if ($find) {
            $title = array_key_exists('title', $shared) && $shared['title'] ? $shared['title'] : $title;
            $histories[] = [
                'name' => $find['name'],
                'path' => $find['path'],
                'end' => $find['path'] == $uri
            ];
            if (array_key_exists('children', $find) && $find['children']) {
                if ($last = collect($find['children'])->filter(fn($item) => $item['path'] == $uri)->first()) {
                    $histories[] = [
                        'name' => $last['name'],
                        'path' => $last['path'],
                        'end' => true
                    ];
                }
            }
        }
        return $histories;
    }

    /**
     * 获取子导航
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $uri
     * @param  array  $menus
     * @return array
     */
    private function covertChildren($user, string $uri, array $menus): array
    {
        $children = [];
        $permissions = [];
        if (!$user)
            return $menus;
        if ($user->is_super) {
            $permissions[] = '*';
        } else {
            $rolePermissions = $user->roles->pluck('permission');
            $permissions = array_unique($rolePermissions->flatten()->all());
        }
        $find = collect($menus)->filter(function ($item) use ($uri) {
            return $item['path'] == $uri || (array_key_exists('children', $item) && collect($item['children'])->filter(fn($_item) => $_item['path'] == $uri)->count());
        })->first();
        if (!$find)
            return [];
        if (!array_key_exists('children', $find))
            return [];
        $children = collect($find['children'])->filter(function ($item) use ($permissions) {
            return (in_array('*', $permissions) || in_array($item['path'], $permissions)) && optional($item)['show'];
        })->map(function ($item) use ($uri) {
            return [
                'name' => $item['name'],
                'path' => $item['path'],
                'current' => $uri == $item['path']
            ];
        })->values()->toArray();
        return $children;
    }
}
