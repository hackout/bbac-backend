<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;

    /**
     * 待提交
     */
    const STATUS_DRAFT = 0;

    /**
     * 待审核
     */
    const STATUS_PENDING = 1;

    /**
     * 审核通过
     */
    const STATUS_SUCCESS = 2;

    /**
     * 拒绝
     */
    const STATUS_REJECT = 3;

    /**
     * 在线考核
     */
    const TYPE_INLINE = 1;

    /**
     * 产品考核
     */
    const TYPE_PRODUCT = 2;

    /**
     * 整车考核
     */
    const TYPE_VEHICLE = 3;

    /**
     * 常规考核
     */
    const SUB_TYPE_STANDARD = 1;

    /**
     * 涂胶考核
     */
    const SUB_TYPE_GLUING = 2;

    /**
     * 动态考核
     */
    const SUB_TYPE_DYNAMIC = 3;

    /**
     * 拆检考核
     */
    const SUB_TYPE_OVERHAUL = 4;

    /**
     * 装配考核
     */
    const SUB_TYPE_ASSEMBLING = 5;

    
    
    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'examine_id',
        'parent_id',
        'version',
        'name',
        'description',
        'engine',
        'period',
        'is_valid',
        'status',
        'type',
        'sub_type',
    ];

    public $casts = [
        'engine' => 'integer',
        'period' => 'integer',
        'is_valid' => 'boolean',
        'status' => 'integer',
        'type' => 'integer',
        'sub_type' => 'integer'
    ];

    public function examine()
    {
        return $this->belongsTo(Examine::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class,'parent_id');
    }

    public function child()
    {
        return $this->belongsTo(self::class,'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class,'author_id');
    }

    public function items()
    {
        return $this->hasMany(CommitItem::class)->orderBy('sort_order','asc');
    }

    public function approves()
    {
        return $this->hasMany(CommitApprove::class)->latest();
    }
}
