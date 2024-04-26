<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 自动添加创建时间
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
trait AutoCreatedTimeTrait
{

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootAutoCreatedTimeTrait()
    {
        static::creating(function (Model $model) {
            $model->timestamps = false;
            $model->created_at = Carbon::now();
        });
        
        static::updating(function (Model $model) {
            $model->timestamps = false;
        });
    }

}