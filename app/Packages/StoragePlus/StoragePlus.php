<?php
namespace App\Packages\StoragePlus;

use FFMpeg\FFProbe;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\Mime\MimeTypes;
use PhpOffice\PhpPresentation\IOFactory as PptIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class StoragePlus
{

    public function __construct(private string $file_name = '.files')
    {
    }

    /**
     * 获取目录文件列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string    $directory
     * @return Collection
     */
    public function getFiles(string $directory): Collection
    {
        $result = Storage::disk('bbac')->get($directory . '/' . $this->file_name) ?? "";
        $result = explode(PHP_EOL, $result);
        $lists = collect([]);
        $names = collect(['id', 'md5', 'name', 'directory', 'path', 'type', 'mimeType', 'size', 'count', 'lastModified', 'hide', 'width', 'height', 'frame', 'duration']);
        foreach ($result as $item) {
            $lists->push($names->combine(array_pad(explode(',', $item), 15, 0)));
        }
        return $lists;
    }

    /**
     * 设置目录文件列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string    $directory
     * @return void
     */
    public function setFiles(string $directory, $file)
    {
        $allFiles = $this->getFiles($directory);
        $allFiles = $allFiles->map(function ($item) use ($file) {
            return $item['id'] == $file['id'] ? $file : $item;
        });
        $this->saveFiles($directory, $allFiles);
    }

    /**
     * 保存目录文件列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $directory
     * @param  Collection $files
     * @return void
     */
    public function saveFiles(string $directory, Collection $files)
    {
        $newFiles = collect([]);
        foreach ($files as $detail) {
            $newFiles->push(implode(',', [
                $detail['id'],
                $detail['md5'],
                $detail['name'],
                $detail['directory'],
                $detail['path'],
                $detail['type'],
                $detail['mimeType'],
                $detail['size'],
                $detail['count'],
                $detail['lastModified'],
                $detail['hide'],
                $detail['width'],
                $detail['height'],
                $detail['frame'],
                $detail['duration'],
            ]));
        }
        Storage::disk('bbac')->put($directory . '/' . $this->file_name, $newFiles->implode(PHP_EOL));
    }

    /**
     * 获取目录详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $directory
     * @return Collection
     */
    public function directoryDetail(string $directory = '/'): Collection
    {
        $directory = Str::replaceFirst('.', '/', $directory);
        if (substr($directory, 0, 1) != '/') {
            $directory = '/' . $directory;
        }
        $truthDirectory = !empty (Str::beforeLast($directory, '/')) ? Str::beforeLast($directory, '/') : '/';
        $files = Storage::disk('bbac')->allFiles($directory);
        $sizeTotal = 0;
        $countTotal = 0;
        $lastModified = null;
        foreach ($files as $file) {
            if (Str::afterLast($file, '/') != $this->file_name) {
                $sizeTotal += Storage::disk('bbac')->size($file);
                $countTotal++;
                $modified = Carbon::createFromTimestamp(Storage::disk('bbac')->lastModified($file));
                if (!$lastModified || $lastModified->gt($modified)) {
                    $lastModified = $modified;
                }
            }
        }
        return collect([
            'md5' => md5($lastModified ?? microtime()),
            'size' => $sizeTotal,
            'count' => $countTotal,
            'lastModified' => optional($lastModified)->timestamp,
            'type' => 'directory',
            'mimeType' => 'directory',
            'directory' => $truthDirectory,
            'path' => $directory,
            'name' => Str::afterLast($directory, '/'),
            'width' => 0,
            'height' => 0,
            'frame' => 0,
            'duration' => 0
        ]);
    }

    public function extensionToString(string $extension):string
    {
        $imageExtensions = ['jpg', 'png', 'gif', 'bmp', 'webp', 'tif', 'tga', 'wmf'];
        $videoExtensions = ['avi', 'wmv', 'mpg', 'mov', 'rm', 'flv', 'mp4', 'h264', 'm3u8', 'vlc', 'f4v'];
        $musicExtensions = ['mp3', 'm3a', 'webm', 'ogg', 'acc', 'wav', 'wma', 'mp3', 'midi'];
        $excelExtensions = ['xls', 'xlsx'];
        $wordExtensions = ['doc', 'docx', 'odf', 'rtf', 'ott'];
        $zipExtensions = ['zip', 'zipx', 'rar', '7z', 'tar', 'gz', 'gz2'];
        if(in_array($extension,$imageExtensions)) return 'image';
        if(in_array($extension,$videoExtensions)) return 'video';
        if(in_array($extension,$musicExtensions)) return 'music';
        if(in_array($extension,$excelExtensions)) return 'excel';
        if(in_array($extension,$wordExtensions)) return 'word';
        if(in_array($extension,$zipExtensions)) return 'zip';
        if($extension == 'ppt' || $extension == 'pptx') return 'ppt';
        if($extension == 'pdf') return 'pdf';
        if($extension == 'exe' || $extension == 'pkg') return 'other';
        return 'unknown';
    }

    /**
     * 获取文件详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $file
     * @return Collection
     */
    public function fileDetail(string $file): Collection
    {
        $mimeType = Storage::disk('bbac')->mimeType($file);
        $imageExtensions = ['jpg', 'png', 'gif', 'bmp', 'webp', 'tif', 'tga', 'wmf'];
        $imageMimeTypes = collect([]);
        foreach ($imageExtensions as $extension) {
            $imageMimeTypes = $imageMimeTypes->concat((array) MimeTypes::getDefault()->getMimeTypes($extension));
        }
        $videoExtensions = ['avi', 'wmv', 'mpg', 'mov', 'rm', 'flv', 'mp4', 'h264', 'm3u8', 'vlc', 'f4v'];
        $videoMimeTypes = collect([]);
        foreach ($videoExtensions as $extension) {
            $videoMimeTypes = $videoMimeTypes->concat((array) MimeTypes::getDefault()->getMimeTypes($extension));
        }
        $musicExtensions = ['mp3', 'm3a', 'webm', 'ogg', 'acc', 'wav', 'wma', 'mp3', 'midi'];
        $musicMimeTypes = collect([]);
        foreach ($musicExtensions as $extension) {
            $musicMimeTypes = $musicMimeTypes->concat((array) MimeTypes::getDefault()->getMimeTypes($extension));
        }
        $excelExtensions = ['xls', 'xlsx'];
        $excelMimeTypes = collect([]);
        foreach ($excelExtensions as $extension) {
            $excelMimeTypes = $excelMimeTypes->concat((array) MimeTypes::getDefault()->getMimeTypes($extension));
        }
        $wordExtensions = ['doc', 'docx', 'odf', 'rtf', 'ott'];
        $wordMimeTypes = collect([]);
        foreach ($wordExtensions as $extension) {
            $wordMimeTypes = $wordMimeTypes->concat((array) MimeTypes::getDefault()->getMimeTypes($extension));
        }
        $zipExtensions = ['zip', 'zipx', 'rar', '7z', 'tar', 'gz', 'gz2'];
        $zipMimeTypes = collect([]);
        foreach ($zipExtensions as $extension) {
            $zipMimeTypes = $zipMimeTypes->concat((array) MimeTypes::getDefault()->getMimeTypes($extension));
        }
        $fileMimeTypes = ['application/octet-stream', 'application/x-ms-dos-executable', 'application/x-msdos-program', 'application/x-msdownload', 'application/x-xar'];
        $pptMimeTypes = ['application/vnd.ms-powerpoint', 'application/mspowerpoint', 'application/powerpoint', 'application/x-mspowerpoint'];
        $pdfMimeTypes = ['application/pdf', 'application/acrobat', 'application/nappdf', 'application/x-pdf', 'image/pdf'];
        if (in_array($mimeType, $imageMimeTypes->all())) {
            $type = 'image';
        } elseif (in_array($mimeType, $videoMimeTypes->all())) {
            $type = 'video';
        } elseif (in_array($mimeType, $musicMimeTypes->all())) {
            $type = 'music';
        } elseif (in_array($mimeType, $excelMimeTypes->all())) {
            $type = 'excel';
        } elseif (in_array($mimeType, $wordMimeTypes->all())) {
            $type = 'word';
        } elseif (in_array($mimeType, $zipMimeTypes->all())) {
            $type = 'zip';
        } elseif (in_array($mimeType, $fileMimeTypes)) {
            $type = 'file';
        } elseif (in_array($mimeType, $pptMimeTypes)) {
            $type = 'ppt';
        } elseif (in_array($mimeType, $pdfMimeTypes)) {
            $type = 'pdf';
        } elseif (Str::before($mimeType, '/') == 'application') {
            $type = 'other';
        } else {
            $type = 'unknown';
        }
        $truthPath = Storage::disk('bbac')->path('/' . $file);
        $width = 0;
        $height = 0;
        $frame = 0;
        $duration = 0;
        if ($type == 'image') {
            $imageInfo = array_pad(@getimagesize($truthPath), 2, 0);
            $width = $imageInfo[0];
            $height = $imageInfo[1];
        }
        if ($type == 'ppt') {
            $ppt = PptIOFactory::load($truthPath);
            $frame = $ppt->getSlideCount();
        }
        if ($type == 'pdf') {
            $pdf = (new PdfParser())->parseFile($truthPath);
            $frame = count($pdf->getPages());
        }
        if ($type == 'music') {
            $ffmpegConfig = [
                'ffmpeg.binaries' => env('FFMPEG_PATH'),
                'ffprobe.binaries' => env('FFPROBE_PATH')
            ];
            $duration = FFProbe::create($ffmpegConfig)->format($truthPath)->get('duration');
        }
        if ($type == 'video') {
            $ffmpegConfig = [
                'ffmpeg.binaries' => env('FFMPEG_PATH'),
                'ffprobe.binaries' => env('FFPROBE_PATH')
            ];
            $ffmpeg = FFMpeg::create($ffmpegConfig);
            $video = $ffmpeg->open($truthPath);
            $stream = $video->getStreams()->first();
            $duration = (int) ceil($stream->get('duration'));
            $frame = (int) $stream->get('nb_frames');
            $width = (int) ceil($stream->get('width'));
            $height = (int) ceil($stream->get('height'));
        }
        return collect([
            'md5' => @md5_file($truthPath),
            'size' => Storage::disk('bbac')->size($file),
            'count' => 1,
            'lastModified' => Storage::disk('bbac')->lastModified($file),
            'type' => $type,
            'mimeType' => $mimeType,
            'directory' => Str::replaceFirst('.', '/', dirname($file)),
            'path' => '/' . $file,
            'name' => Str::afterLast($file, '/'),
            'width' => $width,
            'height' => $height,
            'frame' => $frame,
            'duration' => $duration
        ]);
    }

    /**
     * 重命名文件/文件夹
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $path
     * @param  string  $newPath
     * @return boolean
     */
    public function rename(string $path, string $newPath): bool
    {
        return Storage::disk('bbac')->move($path, $newPath);
    }

    /**
     * 获取新名路径
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $old 带目录文件(夹)名
     * @param  string $new
     * @return string
     */
    public function newName(string $old, string $new): string
    {
        $prefixName = Str::beforeLast($old, '/');
        return $prefixName . '/' . $new;
    }

    /**
     * 文件(夹)是否存在
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $path
     * @return boolean
     */
    public function exists(string $path): bool
    {
        return Storage::disk('bbac')->exists($path);
    }

    /**
     * 删除文件/文件夹
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $path
     * @return boolean
     */
    public function remove(string $path): bool
    {
        if (is_dir(Storage::disk('bbac')->path($path))) {
            return Storage::disk('bbac')->deleteDirectory($path);
        }
        return Storage::disk('bbac')->delete($path);
    }

    /**
     * 获取文件夹路径
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $directory
     * @return string
     */
    public function getDirName(string $directory): string
    {
        if (is_dir(Storage::disk('bbac')->path($directory))) {
            return $directory;
        }
        return dirname($directory);
    }

    /**
     * 获取真实路径
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $directory
     * @return string
     */
    public function getPath(string $directory):string
    {
        return Storage::disk('bbac')->path($directory);
    }

    /**
     * 创建新目录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $directory
     * @param  string $folderName
     * @return bool
     */
    public function createFolder(string $directory, string $folderName): bool
    {
        return Storage::disk('bbac')->makeDirectory($directory . '/' . $folderName);
    }
}