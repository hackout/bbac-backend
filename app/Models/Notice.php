<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 消息模块
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $id 主键
 * @property ?string $user_id 用户ID
 * @property ?string $model_id 关联模型ID
 * @property ?class-string $model_type 关联模型class
 * @property string $title 标题
 * @property int $type 类型
 * @property string $content 内容
 * @property ?string $description 简述
 * @property ?string $from 来源
 * @property bool $is_sent 是否发送
 * @property bool $is_valid 是否有效
 * @property ?array $extra 附加参数
 * @property-read ?string $thumbnail 缩率图
 * @property-read ?User $user 发布者
 * @property-read ?Model $model 其他模块
 * @property-read ?Collection<User> $users 接收者
 * @property-read ?Collection<Media> $media 附件
 */
class Notice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, PrimaryKeyUuidTrait;

    /**
     * 缩率图主键
     */
    const MEDIA_KEY = "thumbnail";

    /**
     * 图片附件主键
     */
    const MEDIA_CONTENT = "thumbnails";

    /**
     * 内容附件主键
     */
    const MEDIA_FILES = "files";

    /**
     * 消息通知
     */
    const MESSAGE = 1;

    /**
     * 站内通知
     */
    const NOTICE = 2;

    /**
     * 工艺变更
     */
    const CHANGE = 3;

    /**
     * 任务通知
     */
    const TASK = 4;

    /**
     * 审批通知
     */
    const APPROVE = 5;

    protected $fillable = [
        'id',
        'user_id',
        'model_id',
        'model_type',
        'title',
        'type',
        'description',
        'content',
        'from',
        'is_sent',
        'is_valid',
        'extra',
    ];

    public $casts = [
        'type' => 'integer',
        'extra' => 'array',
        'is_sent' => 'boolean',
        'is_valid' => 'boolean'
    ];


    public $appends = [self::MEDIA_KEY,'extra_type'];

    public $hidden = ['media'];


    /**
     * 发布者
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * 主图附件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return string|null
     */
    public function getThumbnailAttribute(): ?string
    {
        return $this->getFirstMediaUrl(self::MEDIA_KEY);
    }

    /**
     * 通知接收用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<User>|BelongsToMany|Model
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_notices')->withPivot([
            'is_read'
        ]);
    }

    public function getExtraTypeAttribute()
    {
        if($this->model_type == CommitApprove::class)
        {
            return 'examine';
        }

        if($this->model_type == TorqueChangeRecord::class)
        {
            return 'torque';
        }

        return 'notice';
    }
}
