<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitItemOption extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'sort_order',
        'commit_item_id',
        'name_zh',
        'name_en',
    ];

    /**
     * 考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<CommitItem>|CommitItem|Model
     */
    public function commit_item()
    {
        return $this->belongsTo(CommitItem::class);
    }
}
