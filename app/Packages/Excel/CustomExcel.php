<?php
namespace App\Packages\Excel;

use Illuminate\Support\Collection;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * 自定义Excel导出
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property array $defaultStyle 默认模板格式数据
 * @property array $data BRC格式数据
 */
class CustomExcel
{
    private Spreadsheet $spreadsheet;
    private Xlsx $writer;

    private array $defaultStyle = [];
    private ?Collection $rows;

    private string $endCol = '';

    public function __construct(private string $fileName)
    {
        set_time_limit(0);
        $this->spreadsheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadsheet);
    }


    /**
     * 加载模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $templatePath
     * @return self
     */
    public function loadTemplate(string $templatePath)
    {
        $this->spreadsheet = IOFactory::load($templatePath);
        $this->spreadsheet->setActiveSheetIndex(0);
        $this->writer = new Xlsx($this->spreadsheet);
        return $this;
    }

    /**
     * 尺寸拆分
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string|float|integer $string
     * @return array<int,string>
     */
    protected function getStringSize(string|float|int $string): array
    {
        $sizeType = ['px', 'in', 'cm', 'mm'];
        $result = [
            (float) str_replace($sizeType, '', $string),
            'px'
        ];
        foreach ($sizeType as $type) {
            if (strpos($type, $string) === strlen($string) - 3) {
                $result[1] = $type;
            }
        }
        return $result;
    }

    protected function getBorder(string $str)
    {
        $result = [
            '1px' => Border::BORDER_THIN,
            '2px' => Border::BORDER_HAIR,
            '3px' => Border::BORDER_MEDIUM
        ];

        return array_key_exists($str, $result) ? $result[$str] : Border::BORDER_NONE;
    }

    /**
     * rgb格式转Color
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $color
     * @return Color|bool
     */
    public function rgbToColor(string $color): Color|bool
    {
        $color = str_replace([' ', ' '], '', $color);
        if ($color == 'rgba(0,0,0,0)')
            return false;
        $color = explode(',', str_replace(['rgb', '(', ')'], '', $color));
        return (new Color)->setRGB(strtoupper(sprintf("%02x%02x%02x", $color[0], $color[1], $color[2])));
    }

    /**
     * 是否加粗 
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $string
     * @return boolean
     */
    public function hasBold(string $string): bool
    {
        $boolTypes = [600, 700, 800, 'bold', 'bolder'];
        return in_array($string, $boolTypes);
    }

    /**
     * 是否斜体 
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $string
     * @return boolean
     */
    public function hasItalic(string $string): bool
    {
        return $string == 'italic' ||
            strpos($string, 'italic') !== false ||
            $string == 'oblique' ||
            strpos($string, 'oblique') !== false;
    }

    public function getSize(string $string): int
    {
        return ceil(intval($string) / (4 / 3));
    }

    /**
     * 设置行高
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Worksheet  $sheet
     * @param  Collection $row
     * @return void
     */
    private function drawingRow(Worksheet $sheet, Collection $row)
    {
        $rowSheet = $sheet->getRowDimension($row->get('row'));
        $rowSheet->setRowHeight($row->get('heightSize'), $row->get('heightType'));
    }

    /**
     * 设置列
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Worksheet  $sheet
     * @param  Collection $col
     * @return void
     */
    private function drawingCol(Worksheet $sheet, Collection $col)
    {
        $cell = $sheet->getCell($col->get('cellName'));
        $style = $sheet->getStyle($col->get('cellName'));
        if ($col->get('merge')) {
            $sheet->mergeCells($col->get('mergeRange'));
        }
        if ($col->get('backgroundColor') != 'none' && $col->get('backgroundColor')) {
            $style->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($col->get('backgroundColor'));
        }
        $style->getFont()->setColor($col->get('color'));
        if ($col->get('bold')) {
            $style->getFont()->setBold(true);
        }
        if ($col->get('italic')) {
            $style->getFont()->setItalic(true);
        }
        $style->getFont()->setSize($col->get('fontSize'));
        if ($col->get('alignmentVertical') == 'baseline') {
            $style->getFont()->setBaseLine(true);
        } else {
            $style->getAlignment()->setVertical($col->get('alignmentVertical'));
        }
        $style->getAlignment()->setHorizontal($col->get('alignmentHorizontal'))->setWrapText(true);
        if ($col->get('values')) {
            $self = $this;
            $textData = $col->get('values')->where('type', 'text')->first();
            if ($textData) {
                $textData->put('value', $col->get('values')->where('type', 'text')->pluck('value')->join(""));
                $self->setValue($cell, $textData);
            }
            $imageList = $col->get('values')->where('type', 'img')->values();
            if ($imageList) {
                $imageList->each(function (Collection $value) use ($self, $sheet, $cell) {
                    $self->setImage($cell, $sheet, $value);
                });
            }
        }
    }

    /**
     * 设置内容
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Cell $cell
     * @param  Collection $value
     * @return void
     */
    private function setValue(Cell $cell, Collection $value)
    {
        $cell->setValue($value->get('value'));
        $cell->getStyle()->getFont()
            ->setBold($value->get('bold'))
            ->setItalic($value->get('italic'))
            ->setSize($value->get('size'))
            ->setColor($value->get('color'));
    }

    /**
     * 设置图片
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Cell $cell
     * @param  Collection $value
     * @return void
     */
    private function setImage(Cell $cell, Worksheet $sheet, Collection $value)
    {
        $drawing = new Drawing();
        $drawing->setPath($value->get('path'));
        $drawing->setCoordinates($cell->getCoordinate());
        $drawing->setOffsetX($value->get('left'));
        $drawing->setOffsetY($value->get('top'));
        $drawing->setWorksheet($sheet);
        $drawing->setWidth($value->get('width'));
        $drawing->setHeight($value->get('height'));
        $drawing->setEditAs(Drawing::EDIT_AS_ABSOLUTE);
    }

    public function makeExcel(array $data)
    {
        $this->writer->save($this->fileName);
    }

    public function makeExcelByTemplate(string $template, array $data)
    {
        $templateClass = new ('App\Packages\Excel\Templates\\' . $template);
        $this->spreadsheet = $templateClass->makeExcel($data,$this->spreadsheet);
        $this->writer->save($this->fileName);
    }
}