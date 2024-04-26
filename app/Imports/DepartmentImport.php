<?php

namespace App\Imports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;

class DepartmentImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $collection->each(function ($row, $index) {
            if ($index > 1) {
                foreach ($row as $cell) {
                    list ($parent_id, $name, $contact, $mobile, $email, $leader_id) = array_pad($cell, 6, null);
                    $parent_id && $parent_id = trim($parent_id);
                    $name && $name = trim($name);
                    $contact && $contact = trim($contact);
                    $mobile && $mobile = trim($mobile);
                    $email && $email = trim($email);
                    $leader_id && $leader_id = trim($leader_id);
                    $sql = [
                        'parent_id' => $parent_id,
                        'name' => $name,
                        'contact' => $contact,
                        'mobile' => $mobile,
                        'email' => $email,
                        'leader_id' => $leader_id
                    ];
                    if (!$parent_id) {
                        Department::create($sql);
                    } else {
                        if (Department::find($parent_id)) {
                            Department::create($sql);
                        }
                    }
                }
            }
        });
        $cacheName = (new Department)->getTable();
        $cache = Cache::get($cacheName, []);
        foreach ($cache as $key) {
            Cache::forget($key);
        }
        Cache::forget($cacheName);
    }
}
