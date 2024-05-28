<?php
namespace App\Packages\Excel\Templates;

use App\Models\ExamineProduct;
use App\Models\IssueProduct;
use App\Models\Product;
use App\Models\Task;
use App\Services\Private\DictService;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductDynamicReader
{

    protected Collection $issues;
    protected array $variables = [];
    protected $issuesVariables = [];

    public function __construct(private Task $task)
    {
        $this->issues = IssueProduct::where('task_id', $this->task->id)->get();
        $this->makeVariable();
    }

    public function convertContent($value): string
    {
        $value = str_replace(['#loop#'], '', $value);
        $result = str_replace(array_keys($this->variables), array_values($this->variables), $value);
        return (string) preg_replace("/\{(.*?)\}/i", "", $result);
    }

    public function convertIssueContent($value, int $index = 0): string
    {
        $value = str_replace(['#loop#'], '', $value);
        $result = str_replace(array_keys($this->issuesVariables[$index]), array_values($this->issuesVariables[$index]), $value);
        return (string) preg_replace("/\{(.*?)\}/i", "", $result);
    }

    public function convertLoop(Worksheet $sheet, RowDimension $row): int
    {
        $columns = $sheet->getColumnDimensions();

        if (!$this->issues->count()) {
            foreach ($columns as $column) {
                $cell = $sheet->getCellByColumnAndRow(Coordinate::columnIndexFromString($column->getColumnIndex()), $row->getRowIndex());
                $value = $cell->getValue();

                if ($value) {
                    if ($value instanceof RichText) {
                        $elements = $value->getRichTextElements();
                        foreach ($elements as $element) {
                            $value = htmlspecialchars($element->getText(), Settings::htmlEntityFlags());
                            $element->setText($this->convertContent($value));
                        }
                    } else {
                        $formatCode = $sheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode();
                        $cellData = NumberFormat::toFormattedString($value ?? '', $formatCode ?? NumberFormat::FORMAT_GENERAL, [$this, 'formatColor']);
                        $value = htmlspecialchars($cellData, Settings::htmlEntityFlags());
                        $cell->setValue($this->convertContent($value));
                    }
                }
            }
            return $this->issues->count();
        }

        if ($this->issues->count() > 1) {
            $sheet->insertNewRowBefore($row->getRowIndex(), $this->issues->count() - 1);
        }

        for ($i = 0; $i < $this->issues->count(); $i++) {
            foreach ($columns as $column) {
                $cell = $sheet->getCellByColumnAndRow(Coordinate::columnIndexFromString($column->getColumnIndex()), $row->getRowIndex() + $i);
                $value = $cell->getValue();

                if ($value) {
                    if ($value instanceof RichText) {
                        $elements = $value->getRichTextElements();
                        foreach ($elements as $element) {
                            $value = htmlspecialchars($element->getText(), Settings::htmlEntityFlags());
                            $element->setText($this->convertIssueContent($value, $i));
                        }
                    } else {
                        $formatCode = $sheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode();
                        $cellData = NumberFormat::toFormattedString($value ?? '', $formatCode ?? NumberFormat::FORMAT_GENERAL, [$this, 'formatColor']);
                        $value = htmlspecialchars($cellData, Settings::htmlEntityFlags());
                        $cell->setValue($this->convertIssueContent($value, $i));
                    }
                }
            }
        }

        return $this->issues->count();
    }

    public function toArray(array $template): array
    {
        $result = [];
        foreach ($template as $rs) {
            if ($rs['loop']) {
                foreach ($this->issues as $key => $issue) {
                    $rs['data'] = $rs['data']->toArray();
                    $row = $rs;
                    foreach ($rs['data'] as $keyA => $cell) {
                        $col = json_decode(json_encode($cell),true);
                        foreach ($col['value'] as $key1 => $value) {
                            $col['value'][$key1]['value'] = $this->convertIssueContent($value['value'],$key);
                        }
                        $row['data'][$keyA] = $col;
                    }
                    $result[] = $row;
                }
            } else {
                $row = $rs;
                $row['data'] = [];
                foreach ($rs['data'] as $cell) {
                    $cell['value'] = $cell['value']->toArray();
                    foreach ($cell['value'] as $key => $value) {
                        $cell['value'][$key]['value'] = $this->convertContent($value['value']);
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
        $this->setTaskVariables();
        $this->setDictionaryVariables();
        $this->setExtraVariables();
        $this->setExamineVariables();
        $this->setTaskItemsVariables();
        $this->setIssuesVariables();
        $this->setProductVariables();
    }

    private function setProductVariables()
    {
        $this->variables['{beginning_at}'] = null;
        $this->variables['{examine_at}'] = null;
        $this->variables['{qc_at}'] = null;
        $this->variables['{assembled_at}'] = null;
        $product = Product::where('number', $this->task->eb_number)->first();
        if ($product) {
            $this->variables['{beginning_at}'] = optional($product->beginning_at)->toDateTimeString();
            $this->variables['{examine_at}'] = optional($product->examine_at)->toDateTimeString();
            $this->variables['{qc_at}'] = optional($product->qc_at)->toDateTimeString();
            $this->variables['{assembled_at}'] = optional($product->assembled_at)->toDateTimeString();
        }
    }

    private function setTaskVariables()
    {
        $this->variables['{id}'] = $this->task->id;
        $this->variables['{name}'] = $this->task->name;
        $this->variables['{remark}'] = $this->task->remark;
        $this->variables['{eb_number}'] = $this->task->eb_number;
        $this->variables['{number}'] = $this->task->number;
        $this->variables['{period}'] = $this->task->period;
        $this->variables['{work_date}'] = optional($this->task->start_at)->toDateTimeString();
        $this->variables['{start_at}'] = optional($this->task->start_at)->toDateTimeString();
        $this->variables['{end_at}'] = optional($this->task->end_at)->toDateTimeString();
        $this->variables['{valid_at}'] = optional($this->task->valid_at)->toDateTimeString();
        $this->variables['{created_at}'] = optional($this->task->created_at)->toDateTimeString();
        $this->variables['{auditor}'] = optional(optional($this->task->user)->profile)->name ?? optional($this->task->user)->number;
        $this->variables['{assembly}'] = optional($this->task->assembly)->number;
        $this->variables['{assembly_id}'] = $this->task->assembly_id;
    }

    private function setDictionaryVariables()
    {
        $this->variables['{plant}'] = $this->getDictionaryName('plant', $this->task->plant);
        $this->variables['{line}'] = $this->getDictionaryName('assembly_line', $this->task->line);
        $this->variables['{engine}'] = $this->getDictionaryName('engine_type', $this->task->engine);
        $this->variables['{task_status}'] = $this->getDictionaryName('task_status', $this->task->task_status);
        $this->variables['{status}'] = $this->getDictionaryName('examine_status', $this->task->status);
    }

    private function setExtraVariables()
    {
        if ($this->task->extra && is_array($this->task->extra) && array_key_exists('values', $this->task->extra)) {
            foreach ($this->task->extra['values'] as $key => $rs) {
                $this->variables["{extra.{$key}}"] = $rs;
            }
        }
    }

    private function setExamineVariables()
    {
        if ($this->task->examine instanceof ExamineProduct) {
            $this->variables['{version}'] = $this->task->original_examine['version'];
            $this->variables['{examine_name}'] = $this->task->original_examine['name'];
            $this->variables['{examine_description}'] = $this->task->original_examine['description'];
            $this->variables['{examine_period}'] = $this->task->original_examine['period'];
            $this->variables['{examine_type}'] = $this->getDictionaryName('examine_product_type', $this->task->original_examine['type']);
        }
    }

    private function setTaskItemsVariables()
    {
        if ($this->task->items) {
            foreach ($this->task->items as $item) {
                $this->variables["{{$item->examine_item->unique_id}.index}"] = $item->sort_order;
                $this->variables["{{$item->examine_item->unique_id}.remark}"] = $item->remark;
                try {
                    $content = json_decode($item->content, true);
                } catch (\Throwable $th) {
                    $content = $item->content;
                }
                $this->variables["{{$item->examine_item->unique_id}.status}"] = is_array($content) && array_key_exists('status', $content) ? $content['status'] : null;
                $_dContent = array_key_exists('content', $content) ? $content['content'] : $content['value'];
                $this->variables["{{$item->examine_item->unique_id}}"] = is_array($content) ? (is_array( $_dContent) ?  null :  $_dContent) : $content;
                if (array_key_exists('number', $item->extra) && $item->extra['number']) {
                    for ($i = 1; $i < $item->extra['number']; $i++) {
                        $this->variables["{{$item->examine_item->unique_id}#{$i}}"] = is_array($content) && array_key_exists($i,$_dContent) ? $_dContent[$i] : null;
                    }
                }
            }
        }
    }
    private function setIssuesVariables()
    {
        $issues = IssueProduct::where('task_id', $this->task->id)->get();
        $self = $this;
        $this->issuesVariables = $issues->map(function (IssueProduct $item) use ($self) {
            $defect_level = $self->getDictionaryName('defect_level', $item->defect_level);
            $defect_position = $self->getDictionaryName('question_position', $item->defect_position);
            return [
                '{plant}' => $self->getDictionaryName('plant', $item->plant),
                '{line}' => $self->getDictionaryName('assembly_line', $item->line),
                '{engine}' => $self->getDictionaryName('engine_type', $item->engine),
                '{stage}' => $self->getDictionaryName('assembly_status', $item->stage),
                '{status}' => $self->getDictionaryName('issue_status', $item->status),
                '{defect_description}' => $self->getDictionaryName('defect_category', $item->defect_description),
                '{defect_level}' => $defect_level,
                '{defect_class}' => $defect_level,
                '{defect_category}' => $defect_level,
                '{defect_part}' => $self->getDictionaryName('problem_parts', $item->defect_part),
                '{defect_position}' => $defect_position,
                '{defect_location}' => $defect_position,
                '{defect_cause}' => $self->getDictionaryName('exactly_' . $item->defect_position, $item->defect_cause),
                '{soma}' => $item->soma,
                '{lama}' => $item->lama,
                '{note}' => $item->note,
                '{eight_disciplines}' => $item->eight_disciplines,
                '{is_ok}' => $item->is_ok ? 'OK' : 'NOK',
                '{part_name}' => optional($item->part)->name,
                '{part_number}' => optional($item->part)->number,
            ];
        })->toArray();
    }

    private function getDictionaryName($type, $code)
    {
        return (new DictService())->getNameByCode($type, $code);
    }
}