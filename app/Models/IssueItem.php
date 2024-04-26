<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class IssueItem extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    const MEDIA_FILE = 'file';

    protected $fillable = [
        'id',
        'issue_id',
        'code',
        'content',
    ];


    /**
     * 问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Issue>|Issue|Model
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }


    public $appends = ['thumbnails'];

    public $hidden = ['media'];


    public function getThumbnailsAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_FILE))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => $item->original_url,
                'uuid' => $item->uuid
            ];
        });
    }
}
