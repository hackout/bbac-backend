<?php
namespace App\Packages\PPT;

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Reader\ReaderInterface;
use PhpOffice\PhpPresentation\Writer\WriterInterface;

class PPT
{

    private PhpPresentation $phpPresentation;
    private PhpPresentation $reader;
    private WriterInterface $writer;
    public function __construct(private string $fileName)
    {
        $this->phpPresentation = new PhpPresentation;
        $this->writer = IOFactory::createWriter($this->phpPresentation,'PowerPoint2007');
    }

    public function loadTemplate(string $template)
    {
        $pptReader = IOFactory::createReader('PowerPoint2007');
        $this->reader = $pptReader->load($template);
    }

    public function makeByTemplate(string $template, array $data)
    {
        $templateClass = new ('App\Packages\PPT\Templates\\' . $template);
        $this->phpPresentation = $templateClass->makeTemplate($data, $this->phpPresentation);
        $this->writer->save($this->fileName);
    }
}