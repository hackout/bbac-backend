<?php
namespace App\Services\Backend;

use App\Services\Service;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService extends Service
{

    public function image(UploadedFile $fileBag): array
    {
        $file = Storage::putFile('public/images',$fileBag);
        $result = [
            'url' => Storage::url($file),
            'alt' => $fileBag->getClientOriginalName(),
            'href' => Storage::url($file)
        ];
        return $result;
    }

    public function file(UploadedFile $fileBag): array
    {
        $file = Storage::putFile('public/file',$fileBag);
        $result = [
            'url' => Storage::url($file),
            'alt' => $fileBag->getClientOriginalName(),
            'href' => Storage::url($file)
        ];
        return $result;
    }

    public function video(UploadedFile $fileBag): array
    {
        $file = Storage::putFile('public/video',$fileBag);
        $ffmpegConfig = [
            'ffmpeg.binaries'=>env('FFMPEG_PATH'),
            'ffprobe.binaries' => env('FFPROBE_PATH')
        ];
        $ffmpeg = FFMpeg::create($ffmpegConfig);
        $video = $ffmpeg->open(storage_path('app/'.$file));
        $path = storage_path('app/public/screenshot');
        $posterName = head(explode(".",basename($file))).'.jpg';
        $poster = $path.'/'.$posterName;
        if(!is_dir($path))
        {
            @mkdir($path);
        }
        $frame = $video->frame(TimeCode::fromSeconds(1));
        $frame->save($poster,true);
        $result = [
            'url' => Storage::url($file),
            'poster' => Storage::url('public/screenshot/'.$posterName)
        ];
        return $result;
    }
}