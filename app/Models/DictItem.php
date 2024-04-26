<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 字典键值表
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property int $id 主键
 * @property int $dict_id 字典ID
 * @property string $name 标签
 * @property string $content 键值
 * @property int $sort_order 排序
 * @property ?Carbon $created_at 创建时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read BelongsTo<Dict> $dict 字典
 * 
 */
class DictItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dict_id',
        'name',
        'content',
        'sort_order',
    ];

    public $casts = [
        'dict_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 字典
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Dict>|Dict|Model
     */
    public function dict()
    {
        return $this->belongsTo(Dict::class);
    }
}
