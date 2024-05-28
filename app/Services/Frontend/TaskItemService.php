<?php
namespace App\Services\Frontend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Part;
use App\Models\TaskItem;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskItemService extends Service
{
    public ?string $className = TaskItem::class;

    /**
     * 更新子项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array  $data
     * @param  array  $files
     * @return void
     */
    public function createProduct(
        string $id,
        array $data,
        array $files = [])
    {
        $item = parent::findById($id);
        if ($item) {
            if ($files) {
                foreach ($files as $file) {
                    try {
                        $item->addMedia($file)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                    } catch (\Exception $e) {

                    }
                }
                $item->refresh();
                $data['content']['file'] = array_merge($data['content']['file'],collect($item->files)->map(fn($i) => $i['uri'])->toArray());
            }
            if($data['content']['file'])
            {
                foreach($data['content']['file'] as $fileIndex => $fileUrl)
                {
                    $data['content']['file'][$fileIndex] = str_replace(url('/'),'',$fileUrl);
                }
                $deletes = [];
                foreach($item->files as $rs)
                {
                    if(!in_array(str_replace(url('/'),'',$rs['uri']),$data['content']['file']))
                    {
                        $deletes[] = $rs['uri'];
                    }
                }
                if($deletes)
                {
                    $item->files->each(function(Media $media) use($deletes){
                        if(in_array($media->uuid,$deletes))
                        {
                            $media->delete();
                        }
                    });
                }
            }else{
                $item->media->each(fn(Media $media)=>$media->delete());
            }
            logger(json_encode([$data['content']['file'],$item->files]));
            if(is_array($data['content']))
            {
                $data['content'] = json_encode($data['content']);
            }
            parent::update($id, $data);
        }
    }
}