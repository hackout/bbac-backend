<?php
namespace App\Packages\Task;

use App\Services\Private\DictService;


class OrderNumber
{
    private $engineDictItems = null;
    private $lineDictItems = null;
    private $motorcycleDictItems = null;
    private $statusDictItems = null;
    private $plantDictItems = null;

    public function __construct(private string $rule)
    {
        $this->engineDictItems = (new DictService)->getOptionByCode('engine_type');
        $this->lineDictItems = (new DictService)->getOptionByCode('line');
        $this->motorcycleDictItems = (new DictService)->getOptionByCode('motorcycle_type');
        $this->statusDictItems = (new DictService)->getOptionByCode('assembly_status');
        $this->plantDictItems = (new DictService)->getOptionByCode('plant');
    }

    /**
     * 创建订单号
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  null|int|string  $engine 发动机型号
     * @param  null|int|string  $line 生产线
     * @param  null|int|string  $car 车型
     * @param  null|int|string  $status 阶段状态
     * @param  null|int|string  $plant 工厂
     * @param  null|int|string  $assembly 总成号
     * @param  integer $index
     * @return string
     */
    public function makeOrder($engine = '',$line = '',$car = '',$status = '',$plant = '',$assembly = '',int $index):string
    {
        $engineString = $this->engineDictItems->where('value',$engine)->value('name');
        $lineString = $this->lineDictItems->where('value',$line)->value('name');
        $motorcycleString = $this->motorcycleDictItems->where('value',$car)->value('name');
        $statusString = $this->statusDictItems->where('value',$status)->value('name');
        $plantString = $this->plantDictItems->where('value',$plant)->value('name');
        return str_replace([
            '{YMD}',
            '{YMDHis}',
            '{Engine}',
            '{Line}',
            '{Assembly}',
            '{Factory}',
            '{Car}',
            '{Status}'
        ],[
            date("Ymd"),
            date("YmdHis"),
            $engineString,
            $lineString,
            $assembly,
            $plantString,
            $motorcycleString,
            $statusString
        ],$this->rule).'-'.rand(100,999).'-'.substr(1000 + $index + 1,1);
    }
}