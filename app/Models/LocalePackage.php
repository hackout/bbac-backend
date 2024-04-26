<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'content_zh',
        'content_en',
    ];
}
