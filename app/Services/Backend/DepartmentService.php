<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\Department;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Validation\ValidationException;

/**
 * 组织机构服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DepartmentService extends Service
{
    use ImportTemplateTrait, ExportTemplateTrait;

    public ?string $className = Department::class;

    /**
     * 获取三级组织机构树型结构
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @return array
     */
    public function getFullOptions(User $user):array
    {
        parent::setQuery(function($query) use($user){
            if($user->is_super || optional($user->department)->has_top)
            {
                $query->whereNull('parent_id');
            }else{
                $query->where('parent_id',$user->department_id ?? '0');
            }
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
                                'contact' => $deep->contact,
                                'mobile' => $deep->mobile,
                                'role' => $deep->role,
                                'email' => $deep->email,
                                'parent_id' => $deep->parent_id,
                                'leader_id' => $deep->leader_id,
                            ];
                        })->toArray();
                    }
                    return [
                        'id' => $sub_item->id,
                        'name' => $sub_item->name,
                        'contact' => $sub_item->contact,
                        'mobile' => $sub_item->mobile,
                        'role' => $sub_item->role,
                        'email' => $sub_item->email,
                        'parent_id' => $sub_item->parent_id,
                        'leader_id' => $sub_item->leader_id,
                        'children' => $sub_children
                    ];
                })->toArray();
            }
            return [
                'id' => $item->id,
                'name' => $item->name,
                'contact' => $item->contact,
                'mobile' => $item->mobile,
                'role' => $item->role,
                'email' => $item->email,
                'parent_id' => $item->parent_id,
                'leader_id' => $item->leader_id,
                'children' => $children
            ];
        })->toArray();
    }
    
    /**
     * 获取部门分组
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return array
     */
    public function getList(User $user,array $data):array
    {
        if(!$user->is_super && !optional($user->department)->has_top)
        {
            if(array_key_exists('parent_id',$data) && $data['parent_id'])
            {
                $departmentList = collect($this->getFullOptions($user));
                if(!$departmentList->where('id',$data['parent_id'])->count())
                {
                    throw ValidationException::withMessages(['permission'=>'暂无此权限']);
                }
            }else{
                $data['parent_id'] = $user->department_id;
            }
        }
        parent::setQuery(function($query) use($data){
            if(!array_key_exists('parent_id',$data) || !$data['parent_id'])
            {
                $query->whereNull('parent_id');
            }else{
                $query->where('parent_id',$data['parent_id']);
            }
        });
        return parent::list([
            'id',
            'name',
            'contact',
            'mobile',
            'leader_id',
            'parent_id',
            'role',
            'user_count',
            'email',
        ]);
    }
    
}