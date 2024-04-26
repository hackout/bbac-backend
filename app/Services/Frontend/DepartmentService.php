<?php
namespace App\Services\Frontend;

use App\Models\Department;
use App\Services\Service;

/**
 * 组织机构服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DepartmentService extends Service
{

    public ?string $className = Department::class;

    /**
     * 获取三级组织机构树型结构
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getFullOptions():array
    {
        parent::setQuery(function($query){
            $query->whereNull('parent_id');
        });
        return parent::getAll()->map(function($item){
            $children = null;
            if($item->children && $item->children->count())
            {
                $children = $item->children->map(function($sub_item){
                    $sub_children = null;
                    if($sub_item->children && $sub_item->children->count())
                    {
                        $sub_children = $sub_item->children->map(function($deep){
                            return [
                                'id' => $deep->id,
                                'name' => $deep->name,
                            ];
                        })->toArray();
                    }
                    return [
                        'id' => $sub_item->id,
                        'name' => $sub_item->name,
                        'children' => $sub_children
                    ];
                })->toArray();
            }
            return [
                'id' => $item->id,
                'name' => $item->name,
                'children' => $children
            ];
        })->toArray();
    }

}