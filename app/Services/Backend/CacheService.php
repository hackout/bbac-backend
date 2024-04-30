<?php
namespace App\Services\Backend;

use Cache;
use Illuminate\Support\Facades\Redis;

/**
 * 版本送审服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CacheService
{
    public function getCacheSize()
    {
        set_time_limit(0);
        $type = env('CACHE_DRIVER');
        if ($type == 'redis') {
            return $this->getRedisSize();
        }
        return $this->getFileSize();
    }

    /**
     * 获取缓存统计File
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array<int,int>
     */
    private function getFileSize(): array
    {
        $size = 0;
        $total = 0;
        $storage = Cache::getStore(); // will return instance of FileStore
        $filesystem = $storage->getFilesystem(); // will return instance of Filesystem
        $dir = Cache::getDirectory();
        foreach ($filesystem->allFiles($dir) as $file1) {
            if (is_dir($file1->getPath())) {
                foreach ($filesystem->allFiles($file1->getPath()) as $file2) {
                    $size += $file2->getSize();
                    $total++;
                }
            }
        }
        return [
            'size' => $size,
            'total' => $total
        ];
    }

    /**
     * 获取缓存统计Redis
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array<int,int>
     */
    private function getRedisSize(): array
    {
        $redis = Redis::connection();
        $size = 0;
        $total = 0;
        for ($i = 0; $i < 16; $i++) {
            $db = $redis->select($i);
            $size += $db->getCacheSize();
            $keys = $db->keys("*");
            $total += count($keys);
        }
        return [
            'size' => $size,
            'total' => $total
        ];
    }

    /**
     * 清空系统缓存
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return void
     */
    public function clear()
    {
        set_time_limit(0);
        Cache::clear();
    }

}