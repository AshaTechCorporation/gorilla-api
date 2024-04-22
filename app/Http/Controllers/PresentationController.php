<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Placeholder;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\IOFactory;

class PresentationController extends Controller
{
    public function generatePresentation()
    {

        $objPHPPowerPoint = new PhpPresentation();

        $objPHPPowerPoint->getDocumentProperties()->setCreator('PHPOffice')
            ->setLastModifiedBy('PHPPresentation Team')
            ->setTitle('Sample 19 SlideMaster')
            ->setSubject('Sample 19 Subject')
            ->setDescription('Sample 19 Description')
            ->setKeywords('office 2007 openxml libreoffice odt php')
            ->setCategory('Sample Category');


        // สร้างสไลด์แรก
        $currentSlide = $objPHPPowerPoint->createSlide();

        // เพิ่มเนื้อหาลงในสไลด์แรก
        $shape = $currentSlide->createRichTextShape();
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun('This is slide 1');
        $textRun->getFont()->setBold(true)
            ->setSize(24)
            ->setColor(new Color('FFE06B20'));

        // สร้างสไลด์ที่สอง
        $newSlide = $objPHPPowerPoint->createSlide();

        // เพิ่มเนื้อหาลงในสไลด์ที่สอง
        $shape = $newSlide->createDrawingShape();
        $shape->setName('myImage')
            ->setDescription('My image description')
            ->setPath(public_path('images/image_bank/1b00ebf6a1f373feb00286f40362e5d1.png'))
            ->setHeight(300)
            ->setOffsetX(100)
            ->setOffsetY(100);


        $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
        $oWriterPPTX->save(public_path("/presentation/result") . "/sample.pptx");
    }
}
