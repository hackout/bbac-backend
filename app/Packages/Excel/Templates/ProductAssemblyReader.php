<?php
namespace App\Packages\Excel\Templates;

use App\Models\ExamineProduct;
use App\Models\IssueProduct;
use App\Models\Task;
use App\Services\Private\DictService;
use Illuminate\Database\Eloquent\Collection;

class ProductAssemblyReader
{
    protected Collection $issues = collect([]);
    protected $variables = [];
    protected $issuesVariables = [];

    public function __construct(private Task $task, private array $template)
    {
        $this->issues = IssueProduct::where('task_id', $this->task->id)->get();
        $this->makeVariable();
    }

    public function makeTemplate() :array
    {
        $result = [];
        foreach($this->template as $rs)
        {
            if($rs['loop'])
            {
                foreach($this->issues as $key=>$issue)
                {
                    $row = $rs;
                    foreach($rs['data'] as $cell)
                    {
                        foreach($cell['value'] as $key=>$value)
                        {
                            $cell['value'][$key]['value'] = str_replace(array_keys($this->issuesVariables[$key]),array_values($this->issuesVariables[$key]),$value['value']);
                        }
                        $row['data'][] = $cell;
                    }
                }
            }else{
                $row = $rs;
                $row['data'] = [];
                foreach($rs['data'] as $cell)
                {
                    foreach($cell['value'] as $key=>$value)
                    {
                        $cell['value'][$key]['value'] = str_replace(array_keys($this->variables),array_values($this->variables),$value['value']);
                    }
                    $row['data'][] = $cell;
                }
                $result[] = $row;
            }
        }
        return $result;
    }

    private function makeVariable()
    {
        $this->variables['id'] = $this->task->id;
        $this->variables['name'] = $this->task->name;
        $this->variables['remark'] = $this->task->remark;
        $this->variables['eb_number'] = $this->task->eb_number;
        $this->variables['number'] = $this->task->number;
        $this->variables['period'] = $this->task->period;
        $this->variables['plant'] = (new DictService())->getNameByCode('plant', $this->task->plant);
        $this->variables['line'] = (new DictService())->getNameByCode('assembly_line', $this->task->line);
        $this->variables['engine'] = (new DictService())->getNameByCode('engine_type', $this->task->engine);
        $this->variables['task_status'] = (new DictService())->getNameByCode('task_status', $this->task->task_status);
        $this->variables['status'] = (new DictService())->getNameByCode('examine_status', $this->task->status);
        $this->variables['work_date'] = $this->task->start_at->toDateTimeString();
        $this->variables['start_at'] = $this->task->start_at->toDateTimeString();
        $this->variables['end_at'] = $this->task->end_at->toDateTimeString();
        $this->variables['valid_at'] = $this->task->valid_at->toDateTimeString();
        $this->variables['created_at'] = $this->task->created_at->toDateTimeString();
        $this->variables['auditor'] = optional(optional($this->task->user)->profile)->name ?? optional($this->task->user)->number;
        $this->variables['assembly'] = optional($this->task->assembly)->number;
        $this->variables['assembly_id'] = $this->task->assembly_id;
        $this->variables['created_at'] = $this->task->created_at->toDateTimeString();
        if ($this->task->extra && is_array($this->task->extra) && array_key_exists('values', $this->task->extra)) {
            foreach ($this->task->extra['values'] as $key => $rs) {
                $this->variables["extra.{$key}"] = $rs;
            }
        }
        if ($this->task->examine instanceof ExamineProduct) {
            $this->variables['version'] = $this->task->original_examine['version'];
            $this->variables['examine_name'] = $this->task->original_examine['name'];
            $this->variables['examine_description'] = $this->task->original_examine['description'];
            $this->variables['examine_period'] = $this->task->original_examine['period'];
            $this->variables['examine_type'] = (new DictService())->getNameByCode('examine_product_type', $this->task->original_examine['type']);
        }
        if ($this->task->items) {
            foreach ($this->task->items as $item) {
                $this->variables["{$item->examine_item->unique_id}.index"] = $item->sort_order;
                $this->variables["{$item->examine_item->unique_id}.remark"] = $item->remark;
                $this->variables["{$item->examine_item->unique_id}.status"] = $item->content ? $item->content['status'] : null;
            }
        }
        $this->issuesVariables = $this->issues->map(function (IssueProduct $item) {
            return [
                'plant' => (new DictService())->getNameByCode('plant', $item->plant),
                'line' => (new DictService())->getNameByCode('assembly_line', $item->line),
                'engine' => (new DictService())->getNameByCode('engine_type', $item->engine),
                'stage' => (new DictService())->getNameByCode('assembly_status', $item->stage),
                'status' => (new DictService())->getNameByCode('issue_status', $item->status),
                'defect_description' => (new DictService())->getNameByCode('defect_category', $item->defect_description),
                'defect_level' => (new DictService())->getNameByCode('defect_level', $item->defect_level),
                'defect_part' => (new DictService())->getNameByCode('problem_parts', $item->defect_part),
                'defect_position' => (new DictService())->getNameByCode('question_position', $item->defect_position),
                'defect_cause' => (new DictService())->getNameByCode('exactly_' . $item->defect_position, $item->defect_cause),
                'soma' => $item->soma,
                'lama' => $item->lama,
                'note' => $item->note,
                'eight_disciplines' => $item->eight_disciplines,
                'is_ok' => $item->is_ok ? 'OK' : 'NOK',
                'part_name' => optional($item->part)->name,
                'part_number' => optional($item->part)->number,
            ];
        })->toArray();
    }


}