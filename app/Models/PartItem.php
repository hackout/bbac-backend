<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 零件子项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $part_id 零件ID
 * @property string $assembly_id 总成ID
 * @property string $user_id 用户ID
 * @property string $product_id 发动机ID
 * @property string $number 零件编号
 * @property-read Carbon $created_at 添加时间
 * @property-read Carbon $updated_at 更新时间
 * @property-read Part $part 零件
 * @property-read Assembly $assembly 总成
 * @property-read User $user 用户
 * @property-read Product $product 发动机
 */
class PartItem extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'part_id',
        'assembly_id',
        'user_id',
        'product_id',
        'number'
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 零件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|Part|Model
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * 总成号
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|Assembly|Model
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    /**
     * 录入员
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 发动机
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|Product|Model
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
