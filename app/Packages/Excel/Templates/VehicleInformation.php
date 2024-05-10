<?php
namespace App\Packages\Excel\Templates;

use Str;
use Storage;
use App\Packages\Excel\ExcelPlus;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class VehicleInformation
{
    private Spreadsheet $spreadsheet;
    private $item;
    private $items;
    private $startRow = 10;
    private $startCell = 1;

    private $mergeCell = 10;

    private $marginCell = 2;

    /**
     * 封装数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array       $data
     * @param  Spreadsheet $spreadsheet
     * @return Spreadsheet
     */
    public function makeExcel(array $data, Spreadsheet $spreadsheet): Spreadsheet
    {
        $this->spreadsheet = $spreadsheet;
        $this->items = $data['items'];
        $this->item = [
            "{index}" => $data['index'],
            "{date}" => $data['date'],
            "{shift}" => $data['shift'],
            "{plant}" => $data['plant'],
            "{sensor}" => $data['sensor'],
            "{v_type}" => $data['v_type'],
            "{eb_type}" => $data['eb_type'],
            "{pn}" => $data['pn'],
            "{en_bn}" => $data['en_bn'],
            "{issue_description}" => $data['issue_description'],
            "{root_cause}" => $data['root_cause'],
            "{soma}" => $data['soma'],
            "{status}" => $data['status']
        ];
        $this->makeItem();
        $this->makePicture();
        return $this->spreadsheet;
    }

    private function makePicture()
    {
        if ($this->items) {
            $sheet = $this->spreadsheet->getActiveSheet();
            $that = $this;
            foreach($this->items as $index => $item)
            {
                $rowIndex = $this->startRow;
                $cellIndex = ($index * $this->mergeCell) + ($index * $this->marginCell) + 1;
                $endCell = $cellIndex + $that->mergeCell - 1;
                $borderColor = (new Color)->setRGB('27406A');
                $backgroundColor = (new Color)->setRGB('335CA1');
                $colorColor = (new Color)->setRGB('FFFFFF');
                $cellName = ExcelPlus::numberToLetter($cellIndex).$rowIndex;
                $sheet->setCellValue($cellName,'Picture Index #' .($index + 1));
                $style = $sheet->getStyle($cellName);
                $textRange = Str::replaceArray('?', [ExcelPlus::numberToLetter($cellIndex), $rowIndex, ExcelPlus::numberToLetter($endCell), $rowIndex], '??:??');
                $sheet->mergeCells($textRange);
                $style->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($backgroundColor);
                $style->getFont()->setColor($colorColor);
                $style->getAlignment()->setHorizontal(true);
                $style->getAlignment()->setVertical(true);
                $style->getAlignment()->setWrapText(true);
                $style->getBorders()->getLeft()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $style->getBorders()->getRight()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $style->getBorders()->getTop()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $style->getBorders()->getBottom()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $path = str_replace(env('APP_URL'),'',$item['url']);
                if(strpos($path,'/assets') === 0)
                {
                    $path = public_path($path);
                }
                if(strpos($path,'/storage') === 0)
                {
                    $path = Storage::path(str_replace('/storage','/public',$path));
                }
                $coordinates = Str::replaceArray('?', [ExcelPlus::numberToLetter($cellIndex), $rowIndex + $that->marginCell], '??');
                $style = $sheet->getStyle($coordinates);
                $drawing = new Drawing();
                $drawing->setPath($path);
                $drawing->setCoordinates($coordinates);
                $drawing->setCoordinates2($coordinates);
                $drawing->setOffsetX2(ceil(Font::centimeterSizeToPixels(3.5)));
                $drawing->setOffsetY2(ceil(Font::centimeterSizeToPixels(2)));
                $drawing->setWorksheet($sheet);
                $drawing->setEditAs(Drawing::EDIT_AS_ABSOLUTE);
                $pictureRange = Str::replaceArray('?', [ExcelPlus::numberToLetter($cellIndex), $rowIndex + 2, ExcelPlus::numberToLetter($endCell), $rowIndex + 2], '??:??');
                $sheet->mergeCells($pictureRange);
                $style->getBorders()->getLeft()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $style->getBorders()->getRight()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $style->getBorders()->getTop()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
                $style->getBorders()->getBottom()->setColor($borderColor)->setBorderStyle(Border::BORDER_HAIR);
            }
        }
    }

    private function makeItem()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $rows = $sheet->getRowDimensions();
        $cols = $sheet->getColumnDimensions();
        foreach ($rows as $row) {
            foreach ($cols as $col) {
                $cell = $sheet->getCell($col->getColumnIndex() . $row->getRowIndex());
                if ($value = $cell->getValue()) {
                    $newValue = str_replace(array_keys($this->item), array_values($this->item), $value);
                    if ($value != $newValue) {
                        $cell->setValue($newValue);
                    }
                }
            }
        }
    }
}