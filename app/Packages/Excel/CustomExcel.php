<?php
namespace App\Packages\Excel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Font;

/**
 * 自定义Excel操作
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CustomExcel
{
    private Spreadsheet $spreadsheet;
    private Xlsx $writer;

    public function __construct()
    {
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
        return $this;
    }

    /**
     * 加载数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return self
     */
    public function setDataFormArray(array $data)
    {
        $this->spreadsheet->getActiveSheet()->fromArray($data);
        return $this;
    }

    /**
     * 设置单元格内容
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return self
     */
    public function setData(array $data)
    {
        $active = $this->spreadsheet->getActiveSheet();
        foreach ($data as $key => $value) {
            $active->setCellValue($key, $value);
        }
        return $this;
    }

    /**
     * 读取数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getData(): array
    {
        return $this->spreadsheet->getActiveSheet()->toArray();
    }

    /**
     * 设置单元格样式
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array<string,array<string,string|array<string,string>>> $data
     * @return self
     */
    public function setStyle(array $data)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        foreach ($data as $key => $style) {
            $sheet->getStyle($key)->applyFromArray($style);
        }
        return $this;
    }

    /**
     * 合并单元格
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return self
     */
    public function setMerge(array $data)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        foreach ($data as $merge) {
            $sheet->mergeCells($merge);
        }
        return $this;
    }

    /**
     * 绘图到单元格
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return self
     */
    public function setDrawing(array $data)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        foreach($data as $key=>$image)
        {
            $drawing = new Drawing();
            $drawing->setName('PhpSpreadsheet');
            $drawing->setDescription('PhpSpreadsheet');
            $drawing->setPath($image['path']);
            $drawing->setCoordinates($key);
            $drawing->setCoordinates2($key);
            $drawing->setOffsetX2('60');
            $drawing->setOffsetY2('30');
            $drawing->setWorksheet($sheet);
            $drawing->setEditAs(Drawing::EDIT_AS_ABSOLUTE);
            unset($drawing);
        }
        return $this;
    }

    /**
     * 写入文件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $fileName
     * @return void
     */
    public function save(string $fileName): void
    {
        $this->writer->save($fileName);
    }

    public function setDefault(array $style,string $range,float $width,float $height)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:BZ100')->applyFromArray($style);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPrintArea($range);
        $sheet->setShowGridlines(false);
        $sheet->setPrintGridlines(true);
        $sheet->getDefaultColumnDimension()->setWidth(Font::centimeterSizeToPixels($width),'px');
        $sheet->getDefaultRowDimension()->setRowHeight(Font::centimeterSizeToPixels($height),'px');
        $sheet->getStyle($range)->getBorders()->getOutline()->setColor((new Color)->setRGB('000000'));
        $sheet->getStyle($range)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($range)->getBorders()->getInside()->setColor((new Color)->setRGB('CCCCCC'));
        $sheet->getStyle($range)->getBorders()->getInside()->setBorderStyle(Border::BORDER_HAIR);
    }

    private function writer($col,array $data)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        if($data['coordinate'] != $col)
        {
            $sheet->mergeCells($col.':'.$data['coordinate']);
        }
        $style = $sheet->getStyle($col);
        $styleArray = [
            'font' => [
                'size' => $data['fontSize'],
                'bold' => $data['bold'],
                'italic' => $data['italic'],
                'name' => $data['family']
            ],
            'alignment' => [
                'horizontal' => $data['align'],
                'vertical' => $data['valign'],
                'wrapText' => true,
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['rgb' => ExcelPlus::hashToExcelColor($data['borderLeftColor'])],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['rgb' => ExcelPlus::hashToExcelColor($data['borderRightColor'])],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['rgb' => ExcelPlus::hashToExcelColor($data['borderTopColor'])],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['rgb' => ExcelPlus::hashToExcelColor($data['borderBottomColor'])],
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => ExcelPlus::hashToExcelColor($data['backgroundColor']),
                ]
            ]
        ];
        $style->applyFromArray($styleArray);
        $sheet->getColumnDimension(str_replace([0,1,2,3,4,5,6,7,8,9],'',$col))->setWidth(Font::centimeterSizeToPixels($data['width']),'px');
        $sheet->getRowDimension(str_replace(range('A','Z'),'',$col))->setRowHeight(Font::centimeterSizeToPixels($data['height']),'px');

        if($data['type'] == 'image')
        {
            $drawing = new Drawing();
            $drawing->setPath(public_path($data['value']));
            $drawing->setCoordinates($col);
            $drawing->setCoordinates2($col);
            $drawing->setOffsetX2($data['imageWidth']);
            $drawing->setOffsetY2($data['imageHeight']);
            $drawing->setWorksheet($sheet);
            $drawing->setEditAs(Drawing::EDIT_AS_ABSOLUTE);
        }else{
            if($data['value'])
            {
                $sheet->setCellValue($col,$data['value']);
            }
        }
    }

    /**
     * 根据模板赋值并导入数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @param  array      $template
     * @return self
     */
    public function renderDataFormTemplate(array $data, array $template)
    {

        $defaultStyle = [
            'font' => [
                'size' => (float) $template['info']['fontSize'],
            ],
            'alignment' => [
                'horizontal' => $template['info']['align'],
                'vertical' => $template['info']['valign'],
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $template['info']['borderStyle'],
                    'color' => ['rgb' => ExcelPlus::hashToExcelColor($template['info']['emptyBorder'])],
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => ExcelPlus::hashToExcelColor($template['info']['emptyBackground']),
                ]
            ]
        ];
        $range = Str::replaceArray('?',[ExcelPlus::numberToLetter((int) $template['info']['totalColumns']),$template['info']['totalRows']],'A1:??');
        $this->setDefault($defaultStyle,$range,(float) $template['info']['cellWidth'],(float) $template['info']['rowHeight']);

        $data = collect($data);
        $result = [];

        foreach($template['data'] as $rowKey => $row)
        {
            $rowName = $rowKey + 1;
            $cellKey = null;
            foreach($row as $key => $cell)
            {
                if($cellKey === null)
                {
                    $cellKey = $key;
                }
                $cellName = ExcelPlus::numberToLetter($cellKey + 1);
                if(array_key_exists('col',$cell) && $cell['col'])
                {
                    $cellKey = $cellKey + $cell['col'];
                }
                $resultKey  = $cellName.$rowName;
                $result[$resultKey] = [
                    'value' => array_key_exists('type',$cell) && $cell['type'] == 'variable' ? $data->value(trim($cell['value'])) : (array_key_exists('value', $cell) ? trim($cell['value']) : null),
                    'type' => array_key_exists('type',$cell) && $cell['type'] == 'image' ? 'image' : 'text',
                    'coordinate' => ExcelPlus::numberToLetter($cellKey).$rowName,
                    'fontSize' =>  array_key_exists('fontSize', $cell) ? (int) $cell['fontSize'] : (int) $template['info']['fontSize'],
                    'color' =>  array_key_exists('color', $cell) ? $cell['color'] : $template['info']['color'],
                    'backgroundColor' =>  array_key_exists('backgroundColor', $cell) ? $cell['backgroundColor'] : $template['info']['backgroundColor'],
                    'borderLeftColor' =>  array_key_exists('borderColor', $cell) ? $cell['borderColor'] : $template['info']['borderColor'],
                    'borderRightColor' =>  array_key_exists('borderColor', $cell) ? $cell['borderColor'] : $template['info']['borderColor'],
                    'borderTopColor' =>  array_key_exists('borderColor', $cell) ? $cell['borderColor'] : $template['info']['borderColor'],
                    'borderBottomColor' =>  array_key_exists('borderColor', $cell) ? $cell['borderColor'] : $template['info']['borderColor'],
                    'align' =>  array_key_exists('align', $cell) ? $cell['align'] : $template['info']['align'],
                    'valign' =>  array_key_exists('valign', $cell) ? $cell['valign'] : $template['info']['valign'],
                    'italic' =>  array_key_exists('italic', $cell) ? (bool) $cell['italic'] :  false,
                    'bold' =>  array_key_exists('fontWeight', $cell) ? (int) $cell['fontWeight'] == 600 :  false,
                    'family' =>  array_key_exists('family', $cell) ? $cell['family'] :  'arial',
                    'width' => array_key_exists('width',$cell) ? (float) $cell['width'] : (float) $template['info']['cellWidth'],
                    'height' => array_key_exists('height',$cell) ? (float) $cell['height'] : (float) $template['info']['rowHeight'],
                    'imageWidth' => 0,
                    'imageHeight' => 0,
                ];
                if(array_key_exists('type',$cell) && $cell['type'] == 'empty')
                {
                    $result[$resultKey]['backgroundColor'] = array_key_exists('backgroundColor',$cell) ? $cell['backgroundColor'] : $template['info']['emptyBackground'];
                    $result[$resultKey]['borderLeftColor'] = array_key_exists('borderColor',$cell) ? $cell['borderColor'] : $template['info']['emptyBorder'];
                    $result[$resultKey]['borderRightColor'] = array_key_exists('borderColor',$cell) ? $cell['borderColor'] : $template['info']['emptyBorder'];
                    $result[$resultKey]['borderTopColor'] = array_key_exists('borderColor',$cell) ? $cell['borderColor'] : $template['info']['emptyBorder'];
                    $result[$resultKey]['borderBottomColor'] = array_key_exists('borderColor',$cell) ? $cell['borderColor'] : $template['info']['emptyBorder'];
                }
                if(array_key_exists('borderLeftColor',$cell))
                {
                    $result[$resultKey]['borderLeftColor'] = $cell['borderLeftColor'];
                }
                if(array_key_exists('borderRightColor',$cell))
                {
                    $result[$resultKey]['borderRightColor'] = $cell['borderRightColor'];
                }
                if(array_key_exists('borderTopColor',$cell))
                {
                    $result[$resultKey]['borderTopColor'] = $cell['borderTopColor'];
                }
                if(array_key_exists('borderBottomColor',$cell))
                {
                    $result[$resultKey]['borderBottomColor'] = $cell['borderBottomColor'];
                }
                if(array_key_exists('type',$cell) && $cell['type'] == 'image')
                {
                    $result[$resultKey]['imageWidth'] = ceil(Font::centimeterSizeToPixels(($cell['col'] - 1) * $template['info']['cellWidth'] + $result[$resultKey]['width']));
                    $result[$resultKey]['imageHeight'] = ceil(Font::centimeterSizeToPixels($result[$resultKey]['height']));
                }
            }
        }

        foreach($result as $key=>$rs)
        {
            $this->writer($key,$rs);
        }
        return $this;
    }

}