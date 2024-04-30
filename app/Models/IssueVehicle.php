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
 * @property ?string $id 主键
 * @property ?string $author_id 提交用户ID
 * @property ?string $user_id 处理用户ID
 * @property ?string $master_id 确认工程师ID
 * @property ?string $task_id 考核单ID
 * @property int $shift 班次
 * @property int $plant 生产厂区
 * @property int $eb_type 发动机/电池型号
 * @property string $product_number 生产单号
 * @property int $sensor 问题发现点
 * @property string $eb_number 发动机/电池号
 * @property int $car_line 车系
 * @property int $car_type 车型
 * @property bool $is_block 是否OK
 * @property ?string $description 问题描述
 * @property ?string $initial_analysis 现场分析
 * @property ?string $initial_action 分析行动
 * @property int $block_status 滞留状态
 * @property int $block_content 滞留处理类型
 * @property int $status 问题状态
 * @property int $type 问题类型
 * @property int $defect_level 问题等级
 * @property ?string $soma 短期措施
 * @property ?string $lama 长期措施
 * @property ?string $eight_disciplines 8D
 * @property ?string $ira 责任人
 * @property bool $is_confirm 放行确认
 * @property bool $is_ppm 是否PPM
 * @property bool $is_pre_highlight 是否PPM
 * @property int $detect_area 探测区域
 * @property int $quantity 问题数量
 * @property ?string $cause 根本原因
 * @property ?string $relate_parts 关联零件
 * @property int $cause_type 根本原因类型
 * @property int $block_days 滞留天数
 * @property ?Carbon $due_date 预计交付时间
 * @property ?Carbon $delivery_at 交付时间
 * @property-read float $due_end 时效相差天数
 * @property-read ?Carbon $created_at 提交时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?User $author 提交人
 * @property-read ?Assembly $user 用户
 * @property-read ?Product $master 工程师
 * @property-read ?Part $task 任务单
 * @property-read ?Collection<IssueProductLog> $logs 日志记录
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $overview_attaches 整体图片
 * @property-read ?array<array<string,string>> $detail_attaches 细节图片
 * @property-read ?array<array<string,string>> $master_overview_attaches 整体图片-工程师
 * @property-read ?array<array<string,string>> $master_detail_attaches 细节图片-工程师
 * @property-read ?array<array<string,string>> $videos 视频
 */
class IssueVehicle extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 整体图片
     */
    const MEDIA_OVERVIEW = 'overview';

    /**
     * 细节图片
     */
    const MEDIA_DETAIL = 'detail';

    /**
     * 视频
     */
    const MEDIA_VIDEO = 'video';

    /**
     * 整体图片-工程师
     */
    const MEDIA_MASTER_OVERVIEW = 'master_overview';

    /**
     * 细节图片-工程师
     */
    const MEDIA_MASTER_DETAIL = 'master_detail';

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

    /**
     * 待返修
     */
    const BLOCK_STATUS_PENDING = 1;

    /**
     * 待整车检测
     */
    const BLOCK_STATUS_CHECK = 2;

    /**
     * 已放行
     */
    const BLOCK_STATUS_SUCCESS = 3;

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'master_id',
        'task_id',
        'shift',
        'plant',
        'eb_type',
        'product_number',
        'sensor',
        'eb_number',
        'car_line',
        'car_type',
        'is_block',
        'description',
        'initial_analysis',
        'initial_action',
        'block_status',
        'block_content',
        'status',
        'type',
        'defect_level',
        'soma',
        'lama',
        'eight_disciplines',
        'ira',
        'is_confirm',
        'is_ppm',
        'is_pre_highlight',
        'detect_area',
        'quantity',
        'cause',
        'relate_parts',
        'cause_type',
        'block_days',
        'due_date',
        'delivery_at',
    ];

    public $casts = [
        'shift' => 'integer',
        'plant' => 'integer',
        'sensor' => 'integer',
        'car_line' => 'integer',
        'car_type' => 'integer',
        'block_status' => 'integer',
        'block_content' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'defect_level' => 'integer',
        'detect_area' => 'integer',
        'quantity' => 'integer',
        'cause_type' => 'integer',
        'block_days' => 'integer',
        'is_block' => 'boolean',
        'is_confirm' => 'boolean',
        'is_ppm' => 'boolean',
        'is_pre_highlight' => 'boolean',
        'due_date' => 'datetime',
        'delivery_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public $appends = [
        'overview_attaches',
        'detail_attaches',
        'master_overview_attaches',
        'master_detail_attaches',
        'videos',
        'due_end'
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
     * 工程师
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|BelongsTo
     */
    public function master()
    {
        return $this->belongsTo(User::class, 'master_id');
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
     * 日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<IssueProductLog>|HasMany
     */
    public function logs()
    {
        return $this->hasMany(IssueProductLog::class);
    }

    public function getOverviewAttachesAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_OVERVIEW))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => url($item->getUrl()),
                'uuid' => $item->uuid
            ];
        });
    }
    public function getDetailAttachesAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_DETAIL))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => url($item->getUrl()),
                'uuid' => $item->uuid
            ];
        });
    }
    public function getMasterOverviewAttachesAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_MASTER_OVERVIEW))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => url($item->getUrl()),
                'uuid' => $item->uuid
            ];
        });
    }

    public function getMasterDetailAttachesAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_MASTER_DETAIL))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => url($item->getUrl()),
                'uuid' => $item->uuid
            ];
        });
    }

    public function getVideosAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_VIDEO))
            return [];
        return $medias->map(function ($item) {
            $poster = str_replace('.' . $item->extension, '.jpg', $item->getPath());
            if (file_exists($poster)) {
                $poster = url(str_replace('.' . $item->extension, '.jpg', $item->getUrl()));
            } else {
                $poster = null;
            }
            return [
                'name' => $item->file_name,
                'url' => url($item->getUrl()),
                'uuid' => $item->uuid,
                'poster' => $poster
            ];
        });
    }

    public function getDueEndAttribute(): float
    {
        $now = Carbon::now();
        if (!$this->due_date)
            return 9999;
        if (!$this->delivery_confirm || !$this->delivery_at)
            return $this->due_date->diffInDays($now, true);
        return $this->due_date->diffInDays($this->delivery_at, true);
    }
}
