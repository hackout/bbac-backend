<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * 在线考核-问题追踪-记录
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property ?string $issue_inline_id 提交人ID
 * @property ?string $user_id 用户ID
 * @property string $code 任务ID
 * @property ?array $extra 变更项
 * @property-read ?Carbon $created_at 提交时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?IssueInline $issue 问题追踪
 * @property-read ?User $user 用户
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $thumbnails 附件图片
 */
class IssueInlineLog extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    const MEDIA_FILE = 'file';

    protected $fillable = [
        'id',
        'issue_inline_id',
        'user_id',
        'code',
        'extra',
    ];

    public $casts = [
        'extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $appends = ['thumbnails'];

    public $hidden = ['media'];

    /**
     * 问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<IssueInline>|IssueInline|BelongsTo
     */
    public function issue()
    {
        return $this->belongsTo(IssueInline::class);
    }

    /**
     * 变更人
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getThumbnailsAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_FILE))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => $item->original_url,
                'uuid' => $item->uuid
            ];
        });
    }
}
