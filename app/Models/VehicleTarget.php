<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleTarget extends Model
{
    protected $fillable = [
        'yearly',
        'target',
        'eb_type'
    ];

    public $casts = [
        'target' => 'integer',
        'eb_type' => 'integer'
    ];
}
