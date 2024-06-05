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
 * 产品考核-问题追踪
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property ?string $author_id 提交人ID
 * @property ?string $user_id 用户ID
 * @property ?string $task_id 任务ID
 * @property ?string $task_item_id 任务ID
 * @property int $plant 工厂
 * @property int $line 产线
 * @property int $engine 机型
 * @property int $stage 阶段状态
 * @property ?string $assembly_id 总成ID
 * @property ?string $product_id 发动机ID
 * @property ?string $part_id 零件ID
 * @property int $defect_description 缺陷描述
 * @property int $defect_level 问题等级
 * @property int $defect_part 缺陷零件
 * @property int $defect_position 问题位置
 * @property int $defect_cause 具体位置
 * @property ?string $soma 短期措施
 * @property ?string $lama 长期措施
 * @property ?string $note 备注信息
 * @property ?string $eight_disciplines 8D
 * @property ?string $ira 责任人
 * @property ?string $department 责任部门
 * @property ?int $score_card Score Card
 * @property ?string $cause 原因说明
 * @property int $status 问题状态
 * @property int $type 考核类型
 * @property bool $is_ok OK/NOK
 * @property-read ?Carbon $created_at 提交时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?User $author 提交人
 * @property-read ?User $user 用户
 * @property-read ?Assembly $assembly 总成
 * @property-read ?Product $product 发动机
 * @property-read ?Part $part 零件清单
 * @property-read ?Task $task 任务单
 * @property-read ?TaskItem $task_item 任务单
 * @property-read ?Collection<IssueProductLog> $logs 日志记录
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $defect_attaches 缺陷图片
 * @property-read ?array<array<string,string>> $attachments 附件记录
 */
class IssueProduct extends Model implements HasMedia
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
     * Verify
     */
    const STATUS_VERIFY = 1;

    /**
     * Ongoing
     */
    const STATUS_ONGOING = 2;

    /**
     * Closed
     */
    const STATUS_CLOSED = 3;

    /**
     * Overdue
     */
    const STATUS_OVERDUE = 4;

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'task_id',
        'task_item_id',
        'plant',
        'line',
        'engine',
        'stage',
        'ira',
        'department',
        'score_card',
        'assembly_id',
        'product_id',
        'part_id',
        'defect_description',
        'defect_level',
        'defect_part',
        'defect_position',
        'defect_cause',
        'cause',
        'soma',
        'lama',
        'note',
        'eight_disciplines',
        'status',
        'type',
        'is_ok',
    ];

    public $casts = [
        'plant' => 'integer',
        'line' => 'integer',
        'engine' => 'integer',
        'stage' => 'integer',
        'defect_description' => 'integer',
        'defect_level' => 'integer',
        'defect_part' => 'integer',
        'defect_position' => 'integer',
        'defect_cause' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'score_card' => 'integer',
        'is_ok' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $appends = [
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
     * 任务单子项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<TaskItem>|TaskItem|BelongsTo
     */
    public function task_item()
    {
        return $this->belongsTo(TaskItem::class);
    }

    /**
     * 零件清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Part>|Part|BelongsTo
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * 日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<IssueProductLog>|HasMany
     */
    public function logs()
    {
        return $this->hasMany(IssueProductLog::class);
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