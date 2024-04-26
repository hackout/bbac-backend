<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'available_period',
        'day',
    ];

    public $casts = [
        'period' => 'decimal:2',
        'available_period' => 'decimal:2',
        'day' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(WorkItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
