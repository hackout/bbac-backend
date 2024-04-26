<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class TaskItem  extends Model implements HasMedia
{
    use HasFactory,PrimaryKeyUuidTrait,InteractsWithMedia;

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
        'extra' => 'array'
    ];
    
    /**
     * 考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Task>|Task|Model
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * 考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<ExamineItem>|ExamineItem|Model
     */
    public function examine_item()
    {
        return $this->belongsTo(ExamineItem::class);
    }

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

