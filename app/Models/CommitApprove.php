<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitApprove extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;

    /**
     * 待审核
     */
    CONST STATUS_DRAFT = 0;

    /**
     * 审核中
     */
    CONST STATUS_PENDING = 1;

    /**
     * 审核通过
     */
    CONST STATUS_SUCCESS = 2;

    /**
     * 审核拒绝
     */
    CONST STATUS_REJECT = 3;

    protected $fillable = [
        'id',
        'commit_id',
        'user_id',
        'messenger_id',
        'approver_id',
        'content',
        'influence',
        'concerns',
        'extra',
        'description',
        'status',
    ];

    public $casts = [
        'status' => 'integer',
        'extra' => 'array'
    ];

    public function commit()
    {
        return $this->belongsTo(Commit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messenger()
    {
        return $this->belongsTo(User::class,'messenger_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'approver_id');
    }

    public function notice()
    {
        return $this->morphOne(Notice::class,'model');
    }
}
