<?php
namespace App\Services\Frontend;

use App\Models\Account;
use App\Models\Training;
use App\Models\User;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 培训服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TrainingService extends Service
{

    /**
     * 类型参数
     */
    protected $types = ['safe', 'skill', 'multiple'];


    public ?string $className = Training::class;

    /**
     * 获取有效培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param User $user
     * @return Collection|array
     */
    public function getAllValid(User $user): Collection|array
    {
        parent::setQuery([
            ['status', '>', Training::STATUS_PENDING],
            [function($query) use($user){
                $query->whereIn('id',$user->trainings->pluck('training_id')->toArray());
            }]
        ]);
        $list = parent::getAll();
        $result = [
            'safe' => [],
            'skill' => [],
            'multiple' => []
        ];
        $list->each(function($item) use(&$result,$user){
            $type = self::getNameByType($item->type);
            $result[$type][] = [
                'name' => $item->name,
                'status' => $item->training_users->where('user_id',$user->id)->value('status')
            ];
        });
        return $result;
    }

    /**
     * 查询标记
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer     $type
     * @return string|null
     */
    public static function getNameByType(int $type): ?string
    {
        $self = new self;
        if (!array_key_exists($type - 1, $self->types))
            return null;
        return $self->types[$type - 1];
    }
}