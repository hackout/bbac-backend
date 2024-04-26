<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamineItemOption extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'sort_order',
        'examine_item_id',
        'name_zh',
        'name_en',
    ];

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
}
