<?php
namespace App\Services\Private;

use App\Models\Part;
use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * 零件清单
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PartService extends Service
{
    public ?string $className = Part::class;


    /**
     * 添加零件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function importPart(array $data)
    {
        $sql = [
            'name' => $data['name'],
            'name_en' => $data['name_en'],
            'station' => $data['station'],
            'number' => $data['number'],
            'is_esd' => $data['is_esd'],
            'is_traceability' => $data['is_traceability'],
            'is_one_time' => $data['is_one_time'],
        ];
        $assemblies = array_key_exists('assemblies', $data) && $data['assemblies'] ? $data['assemblies'] : [];
        if (parent::create($sql)) {
            foreach ($assemblies as $assembly) {
                $this->item->assemblies()->attach($assembly['id'], ['num' => $assembly['num']]);
            }
        }
    }

    /**
     * 获取零件选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return Collection
     */
    public function getOption(): Collection
    {
        return parent::getAll()->map(fn(Part $item) => ['value' => $item->id, 'name' => $item->number]);
    }
}