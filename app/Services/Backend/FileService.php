<?php
namespace App\Services\Backend;

use App\Jobs\StorageChanged;
use App\Packages\StoragePlus\StoragePlus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * 重命名文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $path
     * @param  string $name
     * @return void
     */
    public function rename(string $id, string $path, string $name)
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        $newPath = $storage->newName($file['path'], $name);
        if ($file['path'] != $newPath) {
            if ($storage->exists($newPath)) {
                throw ValidationException::withMessages(['new.exists' => '新名称已存在']);
            }
            if ($storage->rename($file['path'], $newPath)) {
                StorageChanged::dispatch($directory);
            } else {
                throw ValidationException::withMessages(['file.not_exists' => '重命名文件失败']);
            }
        }
    }

    /**
     * 移动文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $path
     * @param  string $target
     * @return void
     */
    public function move(string $id, string $path, string $target)
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $target = $storage->getDirName($target);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        $newPath = $target . '/' . $file['name'];
        if ($storage->exists($newPath)) {
            throw ValidationException::withMessages(['new.exists' => '存在同名文件']);
        }
        if ($storage->rename($file['path'], $newPath)) {
            StorageChanged::dispatch($directory);
        } else {
            throw ValidationException::withMessages(['file.not_exists' => '移动文件失败']);
        }
    }

    /**
     * 隐藏/显示文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $id
     * @param  string  $path
     * @param  integer $hide
     * @return void
     */
    public function visit(string $id, string $path, int $hide)
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        $file['hide'] = $hide;
        $storage->setFiles($directory, $file);
    }

    /**
     * 批量移动文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function batchMove(array $data)
    {
        $ids = $data['ids'];
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($data['path']);
        $target = $storage->getDirName($data['target']);
        $files = $storage->getFiles($directory);
        foreach ($ids as $id) {
            $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
            if ($file) {
                $newPath = $target . '/' . $file['name'];
                if (!$storage->exists($newPath)) {
                    $storage->rename($file['path'], $newPath);
                }
            }
        }
        StorageChanged::dispatch($directory);
        StorageChanged::dispatch($target);
    }

    /**
     * 批量删除文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function batchRemove(array $data)
    {
        $ids = $data['ids'];
        $path = $data['path'];
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $newFiles = $files;
        foreach ($ids as $id) {
            $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
            if ($file) {
                if ($storage->remove($file['path'])) {
                    $newFiles = $newFiles->filter(function ($item) use ($id) {
                        return $item['id'] != $id;
                    });
                }
            }
        }
        $storage->saveFiles($directory, $newFiles->values());
    }

    /**
     * 删除文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $path
     * @return void
     */
    public function remove(string $id, string $path)
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        $newFiles = $files->filter(function ($item) use ($id) {
            return $item['id'] != $id;
        });
        if ($storage->remove($file['path'])) {
            $storage->saveFiles($directory, $newFiles->values());
        }
    }

    /**
     * 查看文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $path
     * @return Response
     */
    public function viewer(string $id, string $path):Response
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        return response(Storage::disk('bbac')->get($file['path']))->header('Content-Type', $file['mimeType']);
    }

    /**
     * 下载文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $path
     * @return Response
     */
    public function download(string $id, string $path):Response
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $file = optional($files->filter(fn($n) => $n['id'] == $id))->first();
        if (!$file) {
            throw ValidationException::withMessages(['file.not_exists' => '文件不存在或已删除']);
        }
        return response()->download(Storage::disk('bbac')->path($file['path']));
    }

    /**
     * 创建文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $path
     * @return void
     */
    public function create(string $path)
    {
        $storage = (new StoragePlus(self::FILENAME));
        $directory = $storage->getDirName($path);
        $files = $storage->getFiles($directory);
        $directories = $files->filter(fn($n) => $n['type'] == 'directory')->values()->pluck('name')->toArray();
        $folderName = $this->uniqueFolderName($directories, 'Directory');
        if ($storage->createFolder($directory, $folderName)) {
            StorageChanged::dispatch($directory);
            StorageChanged::dispatch($directory . '/' . $folderName);
        }
    }

    /**
     * 唯一标识
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array  $directories
     * @param  string $folderName
     * @return string
     */
    private function uniqueFolderName(array $directories, string $folderName): string
    {
        if ($directories && in_array($folderName, $directories)) {
            return $this->uniqueFolderName($directories, $folderName . '(1)');
        }
        return $folderName;
    }

    /**
     * 上传文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $path
     * @param  UploadedFile $file
     * @return void
     */
    public function upload(string $path, UploadedFile $file)
    {
        $storage = (new StoragePlus(self::FILENAME));
        $file->move($storage->getPath($path), $file->getClientOriginalName());
        StorageChanged::dispatch($path);
    }
}