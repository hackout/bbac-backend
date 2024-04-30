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
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * 在线考核-问题追踪
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property ?string $author_id 提交人ID
 * @property ?string $user_id 用户ID
 * @property ?string $task_id 任务ID
 * @property int $plant 工厂
 * @property int $line 产线
 * @property int $engine 机型
 * @property int $stage 阶段状态
 * @property ?string $station 工位
 * @property ?string $assembly_id 总成ID
 * @property ?string $product_id 发动机ID
 * @property ?string $affect_scope 影响范围
 * @property ?string $ira 责任人
 * @property int $issue_description 问题描述
 * @property int $defect_level 缺陷等级
 * @property ?string $reason 原因分级
 * @property ?string $cause 根本原因
 * @property int $category 问题等级
 * @property ?string $soma 短期措施
 * @property ?Carbon $soma_due 短期措施-节点
 * @property ?string $lama 长期措施
 * @property ?Carbon $lama_due 长期措施-节点
 * @property ?string $note 备注信息
 * @property ?string $eight_disciplines 8D
 * @property int $status 问题状态
 * @property int $type 考核类型
 * @property-read ?Carbon $created_at 提交时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?User $author 提交人
 * @property-read ?User $user 用户
 * @property-read ?Task $task 任务单
 * @property-read ?Assembly $assembly 总成
 * @property-read ?Product $product 发动机
 * @property-read ?Collection<IssueInlineLog> $logs 日志记录
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $record_attaches 追溯记录
 * @property-read ?array<array<string,string>> $defect_attaches 缺陷图片
 * @property-read ?array<array<string,string>> $attachments 附件记录
 */
class IssueInline extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    /**
     * 缺陷图片
     */
    const MEDIA_DEFECT = 'defect';

    /**
     * 追溯记录
     */
    const MEDIA_RECORD = 'record';

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'task_id',
        'plant',
        'line',
        'engine',
        'stage',
        'station',
        'assembly_id',
        'product_id',
        'affect_scope',
        'ira',
        'issue_description',
        'defect_level',
        'reason',
        'cause',
        'category',
        'soma',
        'soma_due',
        'lama',
        'lama_due',
        'note',
        'eight_disciplines',
        'status',
        'type',
    ];

    public $casts = [
        'plant' => 'integer',
        'line' => 'integer',
        'engine' => 'integer',
        'stage' => 'integer',
        'issue_description' => 'integer',
        'defect_level' => 'integer',
        'category' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'soma_due' => 'date',
        'lama_due' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $appends = [
        'record_attaches',
        'defect_attaches',
        'attachments'
    ];

    public $hidden = ['media'];
    /**
     * 提交人
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
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

    /**
     * 总成
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Assembly>|Assembly|BelongsTo
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    /**
     * 任务单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Task>|Task|BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * 发动机
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Product>|Product|BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<IssueInlineLog>|HasMany
     */
    public function logs()
    {
        return $this->hasMany(IssueInlineLog::class);
    }

    public function getRecordAttachesAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_RECORD))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => $item->original_url,
                'uuid' => $item->uuid
            ];
        });
    }

    public function getDefectAttachesAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_DEFECT))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => $item->original_url,
                'uuid' => $item->uuid
            ];
        });
    }

    public function getAttachmentsAttribute()
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