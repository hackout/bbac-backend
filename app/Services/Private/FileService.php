<?php
namespace App\Services\Private;

use App\Packages\StoragePlus\StoragePlus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public $storage;

    const FILENAME = '.files';

    public function __construct()
    {
        $this->storage = Storage::disk('bbac');
    }

    public function checkStorage()
    {
        $directories = array_merge([''], $this->storage->allDirectories());
        foreach ($directories as $directory) {
            $this->makeFiles('/' . $directory);
        }
    }

    public function makeFiles(string $path)
    {
        $storagePlus = new StoragePlus(self::FILENAME);
        $directories = $this->storage->directories($path);
        $files = $this->storage->files($path);
        $oldList = $storagePlus->getFiles($path);
        $data = collect([]);
        foreach ($directories as $directory) {
            $detail = $storagePlus->directoryDetail($directory);
            $oldFile = $oldList->where('path', $detail['path'])->first();
            $data->push(implode(',', [
                $oldFile ? $oldFile['id'] : Str::uuid(),
                $detail['md5'],
                $detail['name'],
                $detail['directory'],
                $detail['path'],
                $detail['type'],
                $detail['mimeType'],
                $detail['size'],
                $detail['count'],
                $detail['lastModified'],
                $oldFile ? $oldFile['hide'] : 0,
                $detail['width'],
                $detail['height'],
                $detail['frame'],
                $detail['duration'],
            ]));
        }
        foreach ($files as $file) {
            if(Str::afterLast($file,'/') != self::FILENAME)
            {
                $detail = $storagePlus->fileDetail($file);
                $oldFile = $oldList->where('path', $detail['path'])->first();
                $data->push(implode(',', [
                    $oldFile ? $oldFile['id'] : Str::uuid(),
                    $detail['md5'],
                    $detail['name'],
                    $detail['directory'],
                    $detail['path'],
                    $detail['type'],
                    $detail['mimeType'],
                    $detail['size'],
                    $detail['count'],
                    $detail['lastModified'],
                    $oldFile ? $oldFile['hide'] : 0,
                    $detail['width'],
                    $detail['height'],
                    $detail['frame'],
                    $detail['duration'],
                ]));
            }
        }
        $this->storage->put($path . '/' . self::FILENAME, $data->implode(PHP_EOL));
    }
}