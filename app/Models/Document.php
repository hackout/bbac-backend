<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 指导书模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $user_id 用户
 * @property string $name 文件名称
 * @property string $engine 发动机型号
 * @property string $is_valid 是否启用
 * @property string $type 类型
 * @property-read Carbon $created_at 创建时间
 * @property-read Carbon $updated_at 更新时间
 * @property-read Collection<DocumentLog> $logs 日志
 * @property-read Collection<Media> $media 附件
 * @property-read User $user 最近操作用户
 */
class Document extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    const MEDIA_FILE = 'file';

    /**
     * 拆检指导书
     */
    const TYPE_OVERHAUL = 1;

    /**
     * 装配指导书
     */
    const TYPE_ASSEMBLING = 2;
    
    /**
     * 扭矩清单
     */
    const TYPE_TORQUE = 3;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'engine',
        'is_valid',
        'type',
    ];

    public $casts = [
        'engine' => 'integer',
        'is_valid' => 'boolean',
        'type' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 指导书历史记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<DocumentLog>|HasMany
     */
    public function logs()
    {
        return $this->hasMany(DocumentLog::class);
    }

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|User|BelongsTo|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
