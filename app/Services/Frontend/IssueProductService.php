<?php
namespace App\Services\Frontend;

use Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\IssueProduct;
use App\Packages\Department\DepartmentRole;
use App\Services\Service;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 整车服务-问题追踪
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueProductService extends Service
{

    public ?string $className = IssueProduct::class;

    /**
     * 添加产品追踪记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $task_item_id
     * @param  array  $data
     * @param  array  $images
     * @return string
     */
    public function createProduct(string $task_item_id,array $data,array $images):string
    {
        $data['status'] = IssueProduct::STATUS_VERIFY;
        $data['task_item_id'] = $task_item_id;
        $issue = parent::find(['task_item_id'=>$task_item_id,'task_id'=>$data['task_id']]);
        if(!$issue)
        {
            parent::create($data);
            if($this->item)
            {
                foreach($images as $file)
                {
                    $this->item->addMedia($file)->toMediaCollection(IssueProduct::MEDIA_DEFECT);
                }
            }
        }else{
            parent::update($issue->id,$data);
            if($this->item)
            {
                foreach($images as $file)
                {
                    $this->item->addMedia($file)->toMediaCollection(IssueProduct::MEDIA_DEFECT);
                }
            }
        }
        return $this->item->id;
    }
}
