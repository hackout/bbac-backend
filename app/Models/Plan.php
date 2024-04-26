<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'assembly_id',
        'plant',
        'line',
        'type',
        'quantity',
        'actual_quantity',
        'plan_at',
        'is_full',
        'remark',
    ];

    public $casts = [
        'plant' => 'integer',
        'line' => 'integer',
        'type' => 'integer',
        'quantity' => 'integer',
        'actual_quantity' => 'integer',
        'plan_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_full' => 'boolean'
    ];

    /**
     * 总成
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Assembly>|Assembly|Model
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }
}
