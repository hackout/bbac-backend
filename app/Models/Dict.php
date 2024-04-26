<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * 数据字典
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property int $id 主键ID
 * @property string $name 名称
 * @property string $code 字典标识
 * @property string $description 备注
 * @property ?Carbon $created_at 创建时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read ?Collection<DictItem> $items 键值列表
 */
class Dict extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(DictItem::class);
    }
}
