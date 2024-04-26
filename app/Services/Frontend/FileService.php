<?php
namespace App\Services\Frontend;

use App\Packages\StoragePlus\StoragePlus;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class FileService
{
    public $storage;

    const FILENAME = '.files';

    public function __construct()
    {
        $this->storage = Storage::disk('bbac');
    }

    /**
     * 获取文件(夹)列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $path = (string) $data['path'];
        $showHidden = array_key_exists('hidden', $data) ? (int) $data['hidden'] : 0;
        $files = (new StoragePlus(self::FILENAME))->getFiles($path);
        return $files->filter(function ($file) use ($showHidden) {
            return ($showHidden || !$file['hide']) && (!empty ($file['id']));
        })->map(function ($file) {
            return [
                'count' => (int) $file['count'],
                'directory' => (string) $file['directory'],
                'duration' => (int) $file['duration'],
                'frame' => (int) $file['frame'],
                'height' => (int) $file['height'],
                'hide' => (int) $file['hide'],
                'id' => (string) $file['id'],
                'lastModified' => $file['lastModified'] ? Carbon::createFromTimestamp($file['lastModified']) : $file['lastModified'],
                'md5' => (string) $file['md5'],
                'mimeType' => (string) $file['mimeType'],
                'name' => (string) $file['name'],
                'path' => (string) $file['path'],
                'size' => (int) $file['size'],
                'type' => (string) $file['type'],
                'width' => (int) $file['width']
            ];
        })->values()->all();
    }

    /**
     * 查看文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $path
     * @return string
     */
    public function viewer(string $id, string $path): string
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        if (!is_dir(Storage::path('/public/viewer'))) {
            @mkdir(Storage::path('/public/viewer'));
        }
        $path = 'viewer/' . $file['id'] . '.' . Str::afterLast($file['path'],'.');
        $url = Storage::path('/public/' . $path);
        if (file_exists($url))
            return Storage::url($path);
        Storage::put('/public/' . $path, Storage::disk('bbac')->get($file['path']));
        return Storage::url($path);
    }

}