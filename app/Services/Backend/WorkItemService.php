<?php
namespace App\Services\Backend;

use App\Models\Account;
use App\Models\WorkItem;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 考勤服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class WorkItemService extends Service
{

    public ?string $className = WorkItem::class;

    

}