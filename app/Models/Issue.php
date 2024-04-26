<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;
   
    /**
     * 在线考核
     */
    const TYPE_INLINE = 1;
    
    /**
     * 产品考核
     */
    const TYPE_PRODUCT = 2;
    
    /**
     * 整车服务
     */
    const TYPE_SERVICE = 3;

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


    public $fillable = [
        'id',
        'author_id',
        'user_id',
        'model_id',
        'model_type',
        'plant',
        'line',
        'engine',
        'vehicle_type',
        'assembly_id',
        'description',
        'soma',
        'lama',
        'note',
        'eight_disciplines',
        'status',
        'type',
    ];

    public $casts = [
        'plant' => 'integer',
        'line' => 'integer',
        'engine' => 'integer',
        'vehicle_type' => 'integer',
        'assembly_id' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
    ];

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
        return $this->hasMany(IssueItem::class);
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class);

    }

    public function model()
    {
        return $this->morphTo();
    }
}
