<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 培训信息模块
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $name 培训名称
 * @property int $type 类型
 * @property int $status 状态
 * @property int $period 课时(Days)
 * @property ?Carbon $started_at 开始培训时间
 * @property ?Carbon $ended_at 结束培训时间
 * @property ?Carbon $created_at 创建时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read ?Collection $training_users 参与培训人员
 * @property-read ?Collection<Media> $media 附件
 */
class Training extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia, PrimaryKeyUuidTrait;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    /**
     * 安全培训
     */
    const TYPE_SAFE = 1;

    /**
     * 技能培训
     */
    const TYPE_SKILL = 2;

    /**
     * 综合培训
     */
    const TYPE_OTHER = 3;

    /**
     * 未开始
     */
    const STATUS_PENDING = 1;

    /**
     * 进行中
     */
    const STATUS_PROCESSING = 2;

    /**
     * 已完成
     */
    const STATUS_FINISH = 3;

    protected $fillable = [
        'id',
        'name',
        'type',
        'status',
        'started_at',
        'period',
        'ended_at'
    ];

    public $casts = [
        'type' => 'integer',
        'status' => 'integer',
        'period' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 培训人员清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TrainingUser>|HasMany
     */
    public function training_users()
    {
        return $this->hasMany(TrainingUser::class);
    }
}
