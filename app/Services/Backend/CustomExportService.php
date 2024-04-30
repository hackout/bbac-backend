<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\CommitApprove;
use App\Packages\CommitPlus\CommitPlus;

/**
 * 自定义数据导出Excel服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CustomExportService
{

    /**
     * 转换并导出数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array  $data
     * @return string
     */
    public function makeExcel(array $data): string
    {
        dd($data);
        
        return '';
    }

}