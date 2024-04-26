<?php
namespace App\Services\Backend;

use App\Models\TrainingUser;
use App\Models\User;
use Carbon\Carbon;
use App\Services\Service;

/**
 * 员工培训服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TrainingUserService extends Service
{

    public ?string $className = TrainingUser::class;

    /**
     * 设置员工培训状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function updateStatus(array $data,User $user)
    {
        $where = [
            'training_id' => $data['id'],
            'user_id' => $data['user_id']
        ];
        $user = (new UserService)->getDetailByLeader($data['user_id'],$user);
        if(parent::find($where))
        {
            parent::setValue($where,'status',$data['status']);
        }else{
            $where['status'] = $data['status'];
            $where['name'] = $user['item']['name'];
            $where['number'] = $user['item']['number'];
            $where['trained_at'] = Carbon::now();
            parent::create($where);
        }
    }
}