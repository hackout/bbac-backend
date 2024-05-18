<?php
namespace App\Packages\Excel;

use App\Models\Task;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Excel读取器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property array $defaultStyle 默认模板格式数据
 * @property array $data BRC格式数据
 */
class ExcelReader
{
    private Spreadsheet $spreadsheet;
    private Xlsx $reader;

    private array $defaultStyle = [];
    private ?Collection $rows;

    private string $endCol = '';

    public function __construct(private string $fileName)
    {
        set_time_limit(0);
        $this->spreadsheet = IOFactory::load($fileName);
        $this->spreadsheet->setActiveSheetIndex(0);
    }

    public function readData(Task $task,string $templateName)
    {
        $this->spreadsheet->garbageCollect();
        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->calculateColumnWidths();
        [$min, $max] = explode(':', $sheet->calculateWorksheetDataDimension());
        $minRow = Coordinate::indexesFromString($min)[1];
        [$maxCol, $maxRow] = Coordinate::indexesFromString($max);
        $array = [];
        $breaks = [];
        for ($i = $minRow; $i <= $maxRow; $i++) {
            $array[] = $this->convertRow($i, $maxCol, $breaks);
        }
        $array = collect($array)->map(function ($row) {
            $children = collect($row['data']);
            $hasLoop = false;
            $variable = null;
            $row['data'] = $children->map(function ($cell) use (&$hasLoop, &$variable) {
                $values = collect($cell['value']);
                $images = collect($cell['images']);
                $cell['value'] = $values->map(function($value){
                    $value['value'] = str_replace('#loop#','',$value['value']);
                    return $value;
                });
                $cell['images'] = $images;
                if (!$hasLoop) {
                    $values->each(function ($value) use (&$hasLoop, &$variable) {
                        $loop = strpos($value['value'], '#loop#') !== false;
                        if ($loop) {
                            $hasLoop = true;
                            $variable = 'issues';
                        }
                    });
                }
                return $cell;
            });
            $row['loop'] = $hasLoop;
            $row['variable'] = $variable;
            return $row;
        });
        $template = 'App\Packages\Excel\Template\\'.$templateName;
        return ['col' => $this->convertWidth($sheet, $maxCol), 'table' => new $template($task,$array)];
    }

    private function convertWidth($sheet, $max)
    {
        $result = [];
        for ($i = 1; $i <= $max; $i++) {
            $columnDimension = $sheet->getColumnDimension(ExcelPlus::numberToLetter($i));
            $width = SharedDrawing::cellDimensionToPixels($columnDimension->getWidth(), $this->spreadsheet->getDefaultStyle()->getFont());
            if ($width >= 0) {
                $result[] = $width . 'px';
            } else {
                $result[] = '';
            }
        }
        return $result;
    }

    private function convertRow(int $rowIndex, int $maxCol, array &$breaks)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $rowData = $this->spreadsheet->getActiveSheet()->getRowDimension($rowIndex);
        $row = [
            'height' => $rowData->getRowHeight(),
            'index' => $rowData->getRowIndex(),
            'data' => []
        ];
        $array = range(1, $maxCol);
        foreach ($array as $i) {
            $cellKey = ExcelPlus::numberToLetter($i) . $rowIndex;
            if (in_array($cellKey, $breaks)) {
                continue;
            }
            $cell = $sheet->getCell($cellKey);

            [$min, $max] = array_pad(explode(':', $cell->getMergeRange()), 2, null);

            $colspan = 1;
            $rowspan = 1;
            if ($max) {
                [$minCol, $minRow] = Coordinate::indexesFromString($min);
                [$maxCol, $maxRow] = Coordinate::indexesFromString($max);
                $colspan = $maxCol - $minCol + 1;
                $rowspan = $maxRow - $minRow + 1;
            }
            $breaks = array_merge($breaks, $this->makeBreaks($i, $rowIndex, $rowspan, $colspan));
            $value = $this->generateRowCellDataValue($sheet, $cell);
            $cellData = [
                'index' => $i,
                'colspan' => $colspan ?: 1,
                'rowspan' => $rowspan ?: 1,
                'images' => $this->getDrawing($rowIndex, $i),
                'style' => $this->createCSSStyle($cell->getStyle()),
                'value' => $value
            ];
            $row['data'][] = $cellData;
        }
        return $row;
    }

    private function makeBreaks(int $colIndex, int $rowIndex, int $row, int $col)
    {
        $result = [];
        if ($row == 1 && $col == 1) {
            $result[] = ExcelPlus::numberToLetter($colIndex) . $rowIndex;
        } else {
            if ($col > 1) {
                $array = range($colIndex, $colIndex + $col - 1);
                foreach ($array as $i) {
                    $result[] = ExcelPlus::numberToLetter($i) . $rowIndex;
                }
            }
            if ($row > 1) {
                $array1 = range($rowIndex, $rowIndex + $row - 1);
                $array2 = range($colIndex, $colIndex + $col - 1);
                foreach ($array1 as $rowKey) {
                    foreach ($array2 as $i) {
                        $result[] = ExcelPlus::numberToLetter($i) . $rowKey;
                    }
                }
            }
        }
        return array_unique($result);
    }

    /**
     * Create CSS style.
     *
     * @return array
     */
    private function createCSSStyle(Style $style)
    {
        // Create CSS
        return array_merge(
            $this->createCSSStyleAlignment($style->getAlignment()),
            $this->createCSSStyleBorders($style->getBorders()),
            $this->createCSSStyleFont($style->getFont()),
            $this->createCSSStyleFill($style->getFill())
        );
    }


    /**
     * Create CSS style (Fill).
     *
     * @param Fill $fill Fill
     *
     * @return array
     */
    private function createCSSStyleFill(Fill $fill)
    {
        // Construct HTML
        $css = [];

        // Create CSS
        if ($fill->getFillType() !== Fill::FILL_NONE) {
            $value = $fill->getFillType() == Fill::FILL_NONE ?
                'white' : '#' . $fill->getStartColor()->getRGB();
            $css['background-color'] = $value;
        }

        return $css;
    }

    /**
     * Create CSS style.
     *
     * @return array
     */
    private function createCSSStyleAlignment(Alignment $alignment)
    {
        // Construct CSS
        $css = [];

        // Create CSS
        $verticalAlign = $this->mapVAlign($alignment->getVertical() ?? '');
        if ($verticalAlign) {
            $css['vertical-align'] = $verticalAlign;
        }
        $textAlign = $this->mapHAlign($alignment->getHorizontal() ?? '');
        if ($textAlign) {
            $css['text-align'] = $textAlign;
            if (in_array($textAlign, ['left', 'right'])) {
                $css['padding-' . $textAlign] = (string) ((int) $alignment->getIndent() * 9) . 'px';
            }
        }
        $rotation = $alignment->getTextRotation();
        if ($rotation !== 0 && $rotation !== Alignment::TEXTROTATION_STACK_PHPSPREADSHEET) {
            $css['transform'] = "rotate({$rotation}deg)";
        }

        return $css;
    }

    /**
     * Map VAlign.
     *
     * @param string $vAlign Vertical alignment
     *
     * @return string
     */
    private function mapVAlign($vAlign)
    {
        return Alignment::VERTICAL_ALIGNMENT_FOR_HTML[$vAlign] ?? '';
    }

    /**
     * Map HAlign.
     *
     * @param string $hAlign Horizontal alignment
     *
     * @return string
     */
    private function mapHAlign($hAlign)
    {
        return Alignment::HORIZONTAL_ALIGNMENT_FOR_HTML[$hAlign] ?? '';
    }

    /**
     * Create CSS style.
     *
     * @param Borders $borders Borders
     *
     * @return array
     */
    private function createCSSStyleBorders(Borders $borders)
    {
        // Construct CSS
        $css = [];

        // Create CSS
        $css['border-bottom'] = $this->createCSSStyleBorder($borders->getBottom());
        $css['border-top'] = $this->createCSSStyleBorder($borders->getTop());
        $css['border-left'] = $this->createCSSStyleBorder($borders->getLeft());
        $css['border-right'] = $this->createCSSStyleBorder($borders->getRight());

        return $css;
    }

    /**
     * Create CSS style.
     *
     * @param Border $border Border
     */
    private function createCSSStyleBorder(Border $border): string
    {
        //    Create CSS - add !important to non-none border styles for merged cells
        $borderStyle = $this->mapBorderStyle($border->getBorderStyle());

        return $borderStyle . ' #' . $border->getColor()->getRGB() . (($borderStyle == 'none') ? '' : ' !important');
    }


    /**
     * Map border style.
     *
     * @param int|string $borderStyle Sheet index
     *
     * @return string
     */
    private function mapBorderStyle($borderStyle)
    {
        return array_key_exists($borderStyle, Html::BORDER_ARR) ? Html::BORDER_ARR[$borderStyle] : '1px solid';
    }


    private function generateRowCellDataValue(Worksheet $worksheet, Cell $cell): array
    {
        $result = [];
        if ($cell->getValue() instanceof RichText) {
            $result = $this->generateRowCellDataValueRich($cell);
        } else {
            $origData = $cell->getValue();
            $formatCode = $worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode();

            $cellData = NumberFormat::toFormattedString(
                $origData ?? '',
                $formatCode ?? NumberFormat::FORMAT_GENERAL,
                [$this, 'formatColor']
            );
            $value = htmlspecialchars($cellData, Settings::htmlEntityFlags());
            if ($value) {
                $result[] = [
                    'style' => null,
                    'value' => htmlspecialchars($cellData, Settings::htmlEntityFlags())
                ];
            }
        }
        return $result;
    }

    private function generateRowCellDataValueRich(Cell $cell): array
    {
        $elements = $cell->getValue()->getRichTextElements();
        $result = [];
        foreach ($elements as $element) {
            $value = htmlspecialchars($element->getText(), Settings::htmlEntityFlags());
            if ($value) {
                if ($element instanceof Run) {
                    $result[] = [
                        'style' => $this->assembleCSS($this->createCSSStyleFont($element->getFont())),
                        'value' => $value
                    ];
                } else {
                    $result[] = [
                        'style' => null,
                        'value' => $value
                    ];
                }
            }
        }
        return $result;
    }
    /**
     * Create CSS style.
     *
     * @return array
     */
    private function createCSSStyleFont(Font $font)
    {
        // Construct CSS
        $css = [];

        // Create CSS
        if ($font->getBold()) {
            $css['font-weight'] = 'bold';
        }
        if ($font->getUnderline() != Font::UNDERLINE_NONE && $font->getStrikethrough()) {
            $css['text-decoration'] = 'underline line-through';
        } elseif ($font->getUnderline() != Font::UNDERLINE_NONE) {
            $css['text-decoration'] = 'underline';
        } elseif ($font->getStrikethrough()) {
            $css['text-decoration'] = 'line-through';
        }
        if ($font->getItalic()) {
            $css['font-style'] = 'italic';
        }

        $css['color'] = '#' . $font->getColor()->getRGB();
        $css['font-family'] = '\'' . $font->getName() . '\'';
        $css['font-size'] = SharedDrawing::pointsToPixels($font->getSize()) . 'px';

        return $css;
    }

    /**
     * Takes array where of CSS properties / values and converts to CSS string.
     *
     * @return string
     */
    private function assembleCSS(array $values = [])
    {
        $pairs = [];
        foreach ($values as $property => $value) {
            $pairs[] = $property . ':' . $value;
        }
        $string = implode('; ', $pairs);

        return $string;
    }
    private function getDrawing(int $rowIndex, int $colIndex)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $images = [];
        foreach ($sheet->getDrawingCollection() as $drawing) {
            $imageTL = Coordinate::coordinateFromString($drawing->getCoordinates());
            $imageCol = Coordinate::columnIndexFromString($imageTL[0]);
            if ($imageTL[1] == $rowIndex && $imageCol == $colIndex) {

                $fileDesc = $drawing->getDescription();
                $fileDesc = $fileDesc ? htmlspecialchars($fileDesc, ENT_QUOTES) : 'Embedded image';
                if ($drawing instanceof Drawing) {
                    $filename = $drawing->getPath();

                    $filename = (string) preg_replace('/^[.]/', '', $filename);

                    $filename = (string) preg_replace('@^[.]([^/])@', '$1', $filename);

                    $filename = htmlspecialchars($filename, Settings::htmlEntityFlags());
                    $imageSrc = Html::winFileToUrl($filename);
                    $picture = @file_get_contents($filename);
                    if ($picture !== false) {
                        $imageDetails = getimagesize($filename) ?: ['mime' => 'image/png'];
                        $base64 = base64_encode($picture);
                        $imageSrc = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
                    }

                } elseif ($drawing instanceof MemoryDrawing) {
                    $imageResource = $drawing->getImageResource();
                    if ($imageResource) {
                        ob_start();
                        imagepng($imageResource);
                        $contents = (string) ob_get_contents();
                        ob_end_clean();
                        $imageSrc = 'data:image/png;base64,' . base64_encode($contents);
                    }
                }
                $images[] = [
                    'style' => [
                        'position' => 'absolute',
                        'left' => $drawing->getOffsetX() . 'px',
                        'top' => $drawing->getOffsetY() . 'px',
                        'width' => $drawing->getWidth() . 'px',
                        'height' => $drawing->getHeight() . 'px',
                    ],
                    'src' => $imageSrc,
                    'alt' => $fileDesc
                ];
            }
        }
        return $images;
    }
}