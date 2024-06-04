<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleOutbound extends Model
{
    protected $fillable = [
        'daily',
        'outbound',
        'eb_type'
    ];

    public $casts = [
        'outbound' => 'integer',
        'eb_type' => 'integer'
    ];
}
