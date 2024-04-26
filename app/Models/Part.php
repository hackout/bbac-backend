<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 零件清单
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $name 零件名称
 * @property string $name_en 零件名称(英)
 * @property string $station 工位
 * @property string $number 零件编号
 * @property bool $is_esd ESD
 * @property bool $is_traceability 追踪件
 * @property bool $is_one_time OneTime
 * @property-read Carbon $created_at 添加时间
 * @property-read Carbon $updated_at 更新时间
 * @property-read Collection<PartItem> $items 录入零件
 * @property-read Collection<Assembly> $assemblies 关联总成
 */
class Part extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'name',
        'name_en',
        'station',
        'number',
        'is_esd',
        'is_traceability',
        'is_one_time',
    ];

    public $casts = [
        'is_esd' => 'boolean',
        'is_traceability' => 'boolean',
        'is_one_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 零件子项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<PartItem>|HasMany
     */
    public function items()
    {
        return $this->hasMany(PartItem::class);
    }

    /**
     * 相关总成
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Assembly>|BelongsToMany
     */
    public function assemblies()
    {
        return $this->belongsToMany(Assembly::class, 'parts_assemblies')->withPivot(['num']);
    }
}
