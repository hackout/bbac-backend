<?php
namespace App\Packages\PPT\Templates;


use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use PhpOffice\PhpPresentation\Shape\Table;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Slide\SlideLayout;
use PhpOffice\PhpPresentation\Slide\SlideMaster;
use PhpOffice\PhpPresentation\Writer\WriterInterface;
use PhpOffice\PhpPresentation\Shape\Drawing;
use Str;
use Storage;
use App\Packages\Excel\ExcelPlus;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Style\Fill;

class VehicleInformation
{
    private PhpPresentation $phpPresentation;
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
     * @param  PhpPresentation $spreadsheet
     * @return PhpPresentation
     */
    public function makeTemplate(array $data, PhpPresentation $phpPresentation): PhpPresentation
    {
        $this->phpPresentation = $phpPresentation;
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
        return $this->phpPresentation;
    }

    private function makePicture()
    {
        
    }

    private function makeItem()
    {
        $slide = $this->phpPresentation->getActiveSlide();
        $slide->setRelsIndex('Page 1');
        $slide->setIsVisible(true);
        $backgroundShape = new Drawing\File();
        $backgroundShape->setName('Slide Title')
                        ->setPath(resource_path('/ppt/vehicle/bigTitle.png'))
                        ->setHeight(47)
                        ->setWidth(1024)
                        ->setOffsetX(10)
                        ->setOffsetY(10);
        $slide->addShape($backgroundShape);
        $titleShape = $slide->createRichTextShape()
              ->setHeight(30)
              ->setWidth(500)
              ->setOffsetX(100)
              ->setOffsetX(15);
        $textRun = $titleShape->createTextRun("Engine & Battery Q-Loop4 Issue Daily Report");
        $textRun->getFont()
                ->setSize(22)
                ->setColor(new Color('FFFFFFFFF'))
                ->setBold(true);
        $slide2 = new Slide();
        $slide2->setRelsIndex('Page 2');
        $backgroundShape = new Drawing\File();
        $backgroundShape->setName('Slide Title')
                        ->setPath(resource_path('/ppt/vehicle/bigTitle.png'))
                        ->setHeight(47)
                        ->setWidth(1024)
                        ->setOffsetX(10)
                        ->setOffsetY(10);
        $slide2->addShape($backgroundShape);
        $titleShape = $slide2->createRichTextShape()
              ->setHeight(30)
              ->setWidth(500)
              ->setOffsetX(100)
              ->setOffsetX(15);
        $textRun = $titleShape->createTextRun("Engine & Battery Q-Loop4 Issue Daily Report");
        $textRun->getFont()
                ->setSize(22)
                ->setColor(new Color('FFFFFFFFF'))
                ->setBold(true);
        $this->phpPresentation->addSlide($slide2);
    }
}