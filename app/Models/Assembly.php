<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assembly extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    protected $fillable = [
        'id',
        'type',
        'plant',
        'line',
        'status',
        'number',
        'remark'
    ];

    public $casts = [
        'type' => 'integer',
        'plant' => 'integer',
        'line' => 'integer',
        'status' => 'integer'
    ];

    public $appends = ['product_count'];

    public $hidden = ['products'];

    /**
     * 发动机产品
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Product>|HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getProductCountAttribute()
    {
        return $this->products->count();
    }
}
