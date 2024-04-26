<?php
namespace App\Services\Frontend;

use App\Models\User;
use App\Models\UserLog;
use App\Services\Service;
use Illuminate\Support\Str;
use App\Traits\ExportTemplateTrait;

/**
 * 操作日志服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class UserLogService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = UserLog::class;


    /**
     * 增加操作日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User|null $user 用户
     * @param  string    $name 说明
     * @param  boolean   $status 状态
     * @return void
     */
    public function addLog(User $user = null, string $name = null, bool $status = true): void
    {
        if(!request()->route()) return ;
        $reflectionClass = new \ReflectionClass(request()->route()->getController());
        if ($reflectionClass) {
            $method = Str::afterLast(request()->route()->getAction('controller'), '@');
            $document = $reflectionClass->getMethod($method)->getDocComment();
            if ($document && !$name) {
                $name = strtok(Str::of(Str::replace(['/**', '*/', '*', '"', " "], '', $document))->trim(), PHP_EOL);
            }
            if ($name) {
                $os = request()->header('X-Scheme', 'backend');
                $sql = [
                    'user_id' => optional($user)->id ?? '00000000-0000-0000-0000-000000000000',
                    'name' => $name,
                    'route' => request()->fullUrl(),
                    'ip_address' => request()->getClientIp(),
                    'status' => $status,
                    'extra' => [
                            'header' => request()->header(),
                            'input' => request()->input()
                        ],
                    'method' => request()->method(),
                    'os' => $os,
                ];
                $this->create($sql);
            }
        }
    }


    public function getList(array $data = [])
    {
        $conditions = [
            'keyword' => ['search', ['ip_address', 'name', 'route']],
            'user_id' => 'eq',
            'date' => ['datetime_range', 'created_at']
        ];
        $this->listQuery($data, $conditions);
        $this->orderKey = 'created_at';
        $result = $this->list();
        $result['items'] = $result['items']->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => optional(optional($item->user)->profile)->name ?? '游客',
                'username' => $item->user_id,
                'name' => $item->name,
                'route' => $item->route,
                'ip_address' => $item->ip_address,
                'status' => $item->status,
                'extra' => $item->extra,
                'method' => $item->method,
                'os' => $item->os,
                'created_at' => $item->created_at
            ];
        });
        return $result;
    }
}