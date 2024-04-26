<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 扭矩螺栓模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $torque_id 扭矩ID
 * @property string $product_id 发动机ID
 * @property string $number 螺栓编号
 * @property boolean $is_issue 是否问题件
 */
class TorqueItem extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'torque_id',
        'product_id',
        'number',
        'is_issue',
    ];

    public $casts = [
        'is_issue' => 'boolean'
    ];

    /**
     * 发动机
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Product>|Product|Model
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 扭矩数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Torque>|Torque|Model
     */
    public function torque()
    {
        return $this->belongsTo(Torque::class);
    }

    /**
     * 扭矩SPC参数
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItemDetail>|HasMany
     */
    public function torque_item_details()
    {
        return $this->hasMany(TorqueItemDetail::class);
    }

    /**
     * 紧固参数
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItemFixture>|HasMany
     */
    public function torque_item_fixtures()
    {
        return $this->hasMany(TorqueItemFixture::class);
    }

    /**
     * 监控信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItemMonitor>|HasMany
     */
    public function torque_item_monitors()
    {
        return $this->hasMany(TorqueItemMonitor::class);
    }
}
