<?php

namespace App\Imports;

use App\Models\LocalePackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;

class LocalePackageImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $collection->each(function ($row, $index) {
            if ($index > 1) {
                if (trim($row[0])) {
                    LocalePackage::updateOrCreate(['code' => trim($row[0])], [
                        'code' => trim($row[0]),
                        'content_zh' => trim($row[1]),
                        'content_en' => trim($row[2])
                    ]);
                }
            }
        });
        $cacheName = (new LocalePackage)->getTable();
        $cache = Cache::get($cacheName, []);
        foreach ($cache as $key) {
            Cache::forget($key);
        }
        Cache::forget($cacheName);
    }
}
