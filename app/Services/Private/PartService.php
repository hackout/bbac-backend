<?php
namespace App\Services\Private;

use App\Models\Part;
use App\Models\User;
use App\Packages\Department\DepartmentRole;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

}