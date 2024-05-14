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
 * 任务单-考核项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property ?string $user_id 用户ID
 * @property string $task_id 考核单ID
 * @property ?string $examine_item_id 考核项ID
 * @property string $sort_order 排序
 * @property ?string $content 考核内容
 * @property ?string $extra 扩展选项
 * @property ?string $remark 备注信息
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?Task $task 考核单
 * @property-read ?ExamineInlineItem|ExamineProductItem|ExamineVehicleItem $examine_item 考核项
 * @property-read ?User $user 用户
 * @property-read ?Collection<Media> $media 附件
 * 
 */
class TaskItem extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    /**
     * 附件内容Key
     */
    const MEDIA_IMAGE = 'image';


    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'examine_item_id',
        'sort_order',
        'content',
        'extra',
        'remark',
    ];

    public $casts = [
        'sort_order' => 'integer',
        'extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Task>|Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * 考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<ExamineInlineItem|ExamineProductItem|ExamineVehicleItem>|ExamineInlineItem|ExamineProductItem|ExamineVehicleItem
     */
    public function examine_item()
    {
        if (optional($this->task)->type == Task::TYPE_INLINE) {
            return $this->belongsTo(ExamineInlineItem::class, 'examine_item_id');
        }
        if (optional($this->task)->type == Task::TYPE_PRODUCT) {
            return $this->belongsTo(ExamineProductItem::class, 'examine_item_id');
        }
        return $this->belongsTo(ExamineVehicleItem::class, 'examine_item_id');
    }

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

