<?php
namespace App\Services\Frontend;

use App\Models\Account;
use App\Models\Notice;
use App\Models\TrainingUser;
use App\Models\User;
use App\Packages\ImagePlus\ImagePlus;
use Carbon\Carbon;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 员工服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class NoticeService extends Service
{

    public ?string $className = Notice::class;

    public function getList(User $user,array $data):array
    {
        $idList = DB::table('users_notices')->where('user_id',$user->id)->select(['is_read','notice_id'])->get()->pluck('is_read','notice_id')->toArray();
        parent::setQuery([
            [function($query) use($idList){
                $query->whereIn('id',array_keys($idList));
            }],
            ['is_sent','=',true],
            ['is_valid','=',true]
        ]);
        $result = parent::list();
        $result['items'] = $result['items']->map(function($item) use($idList){
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'title' => $item->title,
                'type' => $item->type,
                'from' => $item->form ?? (optional(optional($item->user)->profile)->name ?? optional($item->user)->number),
                'is_read' => $idList[$item->id] == 1
            ];
        });
        return $result;
    }
}