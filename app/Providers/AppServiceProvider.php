<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Model::unguard();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //增加空选项
        Validator::extend(
            'exists_or_null',
            function ($attribute, $value, $parameters) {
                if ($value == 0 || is_null($value)) {
                    return true;
                } else {
                    $validator = Validator::make([$attribute => $value], [
                        $attribute => 'exists:' . implode(",", $parameters)
                    ]);
                    return !$validator->fails();
                }
            }
        );
        //待条件查询
        Validator::extend(
            'exists_plus',
            function ($attribute, $value, $parameters) {
                if(is_string($parameters))
                {
                    $parameters = array_pad(explode(',', $parameters), 4, null);
                }
                list($table, $column, $searchValue, $searchColumn) = array_slice($parameters, 0, 4);
                $where = [$column => $value];
                if ($searchColumn !== null && $searchValue !== null) {
                    $where[$searchColumn] = $searchValue;
                }
                if ($parameters) {
                    if (in_array($value, $parameters)) {
                        return true;
                    }
                }
                return DB::table($table)->where($where)->exists();
            }
        );
    }
}
