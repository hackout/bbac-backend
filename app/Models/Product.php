<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 发动机
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $assembly_id 总成ID
 * @property string $number 发动机号
 * @property int $line 产线
 * @property int $plant 工厂
 * @property int $engine 机型
 * @property int $status 项目阶段
 * @property ?Carbon $beginning_at 接机时间
 * @property ?Carbon $examine_at 考核时间
 * @property ?Carbon $qc_at 试热时间
 * @property ?Carbon $assembled_at 装配时间
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?Assembly $assembly 总成
 */
class Product extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'line',
        'plant',
        'engine',
        'status',
        'assembly_id',
        'number',
        'beginning_at',
        'examine_at',
        'qc_at',
        'assembled_at',
    ];

    public $casts = [
        'beginning_at' => 'datetime',
        'examine_at' => 'datetime',
        'qc_at' => 'datetime',
        'assembled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'line' => 'integer',
        'plant' => 'integer',
        'engine' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 发动机总成
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Assembly>|Assembly|Model
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

}
