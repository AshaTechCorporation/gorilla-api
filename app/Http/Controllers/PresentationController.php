<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Placeholder;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails;

class PresentationController extends Controller
{
    private $defaultFontName;
    private $imageWidth;
    private $imageHeight;
    private $presentation;

    public function __construct()
    {
        // Initialize class properties
        $this->defaultFontName = 'Calibri';
        $this->imageWidth = 960;
        $this->imageHeight = 540;

        // Create a new presentation
        $this->presentation = new PhpPresentation();
        $this->presentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);
    }
    public function Thumbnail()
    {
        //  ************************* Add the first slide *************************
        $firstSlide = $this->presentation->getActiveSlide();

        // Set the slide background color (optional)
        $imagePath = public_path("/presentation/static/bg") . "/thumbnail.png";
        // Set the background image for the first slide
        $backgroundImage = $firstSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // first component
        $textShapeWidth = 350;
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2;

        // Add dynamic content to the first slide
        $textShape = $firstSlide->createRichTextShape();
        $textShape->setHeight(120);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(20);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('REPORT');
        $textRun->getFont()->setSize(48);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName($this->defaultFontName);


        // second component
        $textShapeWidth = 900; // Width of the text shape
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2; // Calculate the offset to center horizontally

        // Add dynamic content to the first slide
        $textShape = $firstSlide->createRichTextShape();
        $textShape->setHeight(150);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(200);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('ProductName');
        $textRun->getFont()->setSize(86);
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName('Berlin Sans FB');

        // Add shadow effect to the text shape
        $textShape->getShadow()->setVisible(true);
        $textShape->getShadow()->setDirection(180); // Angle of shadow (in degrees)
        $textShape->getShadow()->setDistance(8); // Distance of shadow from shape
        $textShape->getShadow()->setBlurRadius(2); // Blur radius of shadow
        $textShape->getShadow()->setColor(new Color('FFE06B20')); // Color of shadow

        // third component
        $textShapeWidth = 250; // Width of the text shape
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2; // Calculate the offset to center horizontally


        // Add dynamic content to the first slide
        $textShape = $firstSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(400);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('E1AB16'))->setEndColor(new Color('E1AB16'));

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('3 Month 2024');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName($this->defaultFontName);
    }

    public function Status()
    {
        //  *************************Add the second slide *************************
        $imagePath = public_path("/presentation/static/bg") . "/b1.jpg";
        $secondSlide = $this->presentation->createSlide();

        // Set the slide background color (optional)

        $backgroundImage = $secondSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // first component
        $textShapeWidth = 500;
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2;

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(120);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(20);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Total State');
        $textRun->getFont()->setSize(48);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName('Berlin Sans FB');

        // Add shadow effect to the text shape
        $textShape->getShadow()->setVisible(true);
        $textShape->getShadow()->setDistance(6); // Distance of shadow from shape
        $textShape->getShadow()->setBlurRadius(5); // Blur radius of shadow
        $textShape->getShadow()->setColor(new Color('E11616')); // Color of shadow
        // second component
        $textShapeWidth = 150;
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2;

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(120);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('E1AB16'))->setEndColor(new Color('E1AB16'));

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Overall');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName($this->defaultFontName);

        // Add an view image to the second slide
        $shape = $secondSlide->createDrawingShape();
        $shape->setName('view')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/view.png'))
            ->setHeight(100)
            ->setWidth(100)
            ->setOffsetX(112)
            ->setOffsetY(220);


        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(320);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('view');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(100);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(420);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('737373'))->setEndColor(new Color('737373'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add an like image to the second slide
        $shape = $secondSlide->createDrawingShape();
        $shape->setName('like')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/like.png'))
            ->setHeight(100)
            ->setWidth(100)
            ->setOffsetX(324)
            ->setOffsetY(220);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(324);
        $textShape->setOffsetY(320);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('like');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(100);
        $textShape->setOffsetX(324);
        $textShape->setOffsetY(420);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('cd6966'))->setEndColor(new Color('cd6966'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add an comment image to the second slide
        $shape = $secondSlide->createDrawingShape();
        $shape->setName('comment')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/comment.png'))
            ->setHeight(100)
            ->setWidth(100)
            ->setOffsetX(536)
            ->setOffsetY(220);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(150);
        $textShape->setOffsetX(511);
        $textShape->setOffsetY(320);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('comment');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(100);
        $textShape->setOffsetX(536);
        $textShape->setOffsetY(420);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('65990b'))->setEndColor(new Color('65990b'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add an share image to the second slide
        $shape = $secondSlide->createDrawingShape();
        $shape->setName('share')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/share.png'))
            ->setHeight(100)
            ->setWidth(100)
            ->setOffsetX(748)
            ->setOffsetY(220);


        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(748);
        $textShape->setOffsetY(320);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('share');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(100);
        $textShape->setOffsetX(748);
        $textShape->setOffsetY(420);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);
    }

    public function Influ_format_3()
    {
    }
    public function Insertdata()
    {
    }
    public function Topperform()
    {
    }

    public function Test_newppx()
    {
        $this->Thumbnail();
        $this->Status();
        // Save the presentation
        $dynamicPresentationPath = public_path("/presentation/result") . "/sample.pptx";
        $objWriter = IOFactory::createWriter($this->presentation, 'PowerPoint2007');
        $objWriter->save($dynamicPresentationPath);
    }
    public function generatePresentation()
    {
        $defaultFontName = 'Calibri'; // Use a common font
        $imageWidth = 960;
        $imageHeight = 540;

        // Create a new presentation
        $presentation = new PhpPresentation();

        // $presentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);
        // //  ************************* Add the first slide *************************
        // $firstSlide = $presentation->getActiveSlide();

        // // Set the slide background color (optional)
        // $imagePath = public_path("/presentation/static/bg") . "/thumbnail.png";
        // $imageWidth = 960;
        // $imageHeight = 540;
        // $defaultFontName = 'Calibri'; // Use a common font
        // // Set the background image for the first slide
        // $backgroundImage = $firstSlide->createDrawingShape();
        // $backgroundImage->setPath($imagePath);
        // $backgroundImage->setWidth($imageWidth);
        // $backgroundImage->setHeight($imageHeight);
        // $backgroundImage->setOffsetX(0);
        // $backgroundImage->setOffsetY(0);

        // // first component
        // $textShapeWidth = 350;
        // $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

        // // Add dynamic content to the first slide
        // $textShape = $firstSlide->createRichTextShape();
        // $textShape->setHeight(120);
        // $textShape->setWidth($textShapeWidth);
        // $textShape->setOffsetX($textShapeOffsetX);
        // $textShape->setOffsetY(20);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('REPORT');
        // $textRun->getFont()->setSize(48);
        // $textRun->getFont()->setColor(new Color('FFFFFF'));
        // $textRun->getFont()->setName($defaultFontName);


        // // second component
        // $textShapeWidth = 900; // Width of the text shape
        // $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2; // Calculate the offset to center horizontally

        // // Add dynamic content to the first slide
        // $textShape = $firstSlide->createRichTextShape();
        // $textShape->setHeight(150);
        // $textShape->setWidth($textShapeWidth);
        // $textShape->setOffsetX($textShapeOffsetX);
        // $textShape->setOffsetY(200);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('ProductName');
        // $textRun->getFont()->setSize(86);
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setColor(new Color('FFFFFF'));
        // $textRun->getFont()->setName('Berlin Sans FB');

        // // Add shadow effect to the text shape
        // $textShape->getShadow()->setVisible(true);
        // $textShape->getShadow()->setDirection(180); // Angle of shadow (in degrees)
        // $textShape->getShadow()->setDistance(8); // Distance of shadow from shape
        // $textShape->getShadow()->setBlurRadius(2); // Blur radius of shadow
        // $textShape->getShadow()->setColor(new Color('FFE06B20')); // Color of shadow

        // // third component
        // $textShapeWidth = 250; // Width of the text shape
        // $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2; // Calculate the offset to center horizontally


        // // Add dynamic content to the first slide
        // $textShape = $firstSlide->createRichTextShape();
        // $textShape->setHeight(50);
        // $textShape->setWidth($textShapeWidth);
        // $textShape->setOffsetX($textShapeOffsetX);
        // $textShape->setOffsetY(400);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('E1AB16'))->setEndColor(new Color('E1AB16'));

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('3 Month 2024');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setName($defaultFontName);


        // //  *************************Add the second slide *************************
        // $imagePath = public_path("/presentation/static/bg") . "/b1.jpg";
        // $secondSlide = $presentation->createSlide();

        // // Set the slide background color (optional)

        // $backgroundImage = $secondSlide->createDrawingShape();
        // $backgroundImage->setPath($imagePath);
        // $backgroundImage->setWidth($imageWidth);
        // $backgroundImage->setHeight($imageHeight);
        // $backgroundImage->setOffsetX(0);
        // $backgroundImage->setOffsetY(0);

        // // first component
        // $textShapeWidth = 500;
        // $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(120);
        // $textShape->setWidth($textShapeWidth);
        // $textShape->setOffsetX($textShapeOffsetX);
        // $textShape->setOffsetY(20);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('Total State');
        // $textRun->getFont()->setSize(48);
        // $textRun->getFont()->setColor(new Color('FFFFFF'));
        // $textRun->getFont()->setName('Berlin Sans FB');

        // // Add shadow effect to the text shape
        // $textShape->getShadow()->setVisible(true);
        // $textShape->getShadow()->setDistance(6); // Distance of shadow from shape
        // $textShape->getShadow()->setBlurRadius(5); // Blur radius of shadow
        // $textShape->getShadow()->setColor(new Color('E11616')); // Color of shadow
        // // second component
        // $textShapeWidth = 150;
        // $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(50);
        // $textShape->setWidth($textShapeWidth);
        // $textShape->setOffsetX($textShapeOffsetX);
        // $textShape->setOffsetY(120);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('E1AB16'))->setEndColor(new Color('E1AB16'));

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('Overall');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new Color('FFFFFF'));
        // $textRun->getFont()->setName($defaultFontName);

        // // Add an view image to the second slide
        // $shape = $secondSlide->createDrawingShape();
        // $shape->setName('view')
        //     ->setDescription('My image description')
        //     ->setPath(public_path('/presentation/static/view.png'))
        //     ->setHeight(100)
        //     ->setWidth(100)
        //     ->setOffsetX(112)
        //     ->setOffsetY(220);


        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(60);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(112);
        // $textShape->setOffsetY(320);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('view');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(50);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(112);
        // $textShape->setOffsetY(420);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('737373'))->setEndColor(new Color('737373'));
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('123');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add an like image to the second slide
        // $shape = $secondSlide->createDrawingShape();
        // $shape->setName('like')
        //     ->setDescription('My image description')
        //     ->setPath(public_path('/presentation/static/like.png'))
        //     ->setHeight(100)
        //     ->setWidth(100)
        //     ->setOffsetX(324)
        //     ->setOffsetY(220);

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(60);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(324);
        // $textShape->setOffsetY(320);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('like');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(50);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(324);
        // $textShape->setOffsetY(420);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('cd6966'))->setEndColor(new Color('cd6966'));
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('123');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add an comment image to the second slide
        // $shape = $secondSlide->createDrawingShape();
        // $shape->setName('comment')
        //     ->setDescription('My image description')
        //     ->setPath(public_path('/presentation/static/comment.png'))
        //     ->setHeight(100)
        //     ->setWidth(100)
        //     ->setOffsetX(536)
        //     ->setOffsetY(220);

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(60);
        // $textShape->setWidth(150);
        // $textShape->setOffsetX(511);
        // $textShape->setOffsetY(320);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('comment');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(50);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(536);
        // $textShape->setOffsetY(420);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('65990b'))->setEndColor(new Color('65990b'));
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('123');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add an share image to the second slide
        // $shape = $secondSlide->createDrawingShape();
        // $shape->setName('share')
        //     ->setDescription('My image description')
        //     ->setPath(public_path('/presentation/static/share.png'))
        //     ->setHeight(100)
        //     ->setWidth(100)
        //     ->setOffsetX(748)
        //     ->setOffsetY(220);


        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(60);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(748);
        // $textShape->setOffsetY(320);
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('share');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // // Add dynamic content to the first slide
        // $textShape = $secondSlide->createRichTextShape();
        // $textShape->setHeight(50);
        // $textShape->setWidth(100);
        // $textShape->setOffsetX(748);
        // $textShape->setOffsetY(420);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        // $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('123');
        // $textRun->getFont()->setSize(20);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        // $textRun->getFont()->setBold(true);
        // $textRun->getFont()->setName($defaultFontName);

        // ************************* Add the third slide *****************************
        $imagePath = public_path("/presentation/static/bg") . "/b3.JPG";
        $thirdSlide = $presentation->createSlide();

        // Set the slide background color (optional)
        $backgroundImage = $thirdSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($imageWidth);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // Influencer 1
        // Add dynamic content to the first slide
        $textShape =  $thirdSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX(126);
        $textShape->setOffsetY(110);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Influencer1');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/influ1.jpg'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(52)
            ->setOffsetY(220);

        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/tiktok.png'))
            ->setHeight(60)
            ->setWidth(60)
            ->setOffsetX(81)
            ->setOffsetY(105);

        // Influencer 2
        // Add dynamic content to the first slide
        $textShape =  $thirdSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX(402);
        $textShape->setOffsetY(110);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Influencer2');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/influ2.jpg'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(354)
            ->setOffsetY(220);


        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/tiktok.png'))
            ->setHeight(60)
            ->setWidth(60)
            ->setOffsetX(357)
            ->setOffsetY(105);
        // Influencer 3
        // Add dynamic content to the first slide
        $textShape =  $thirdSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX(708);
        $textShape->setOffsetY(110);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Influencer3');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/influ3.jpg'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(656)
            ->setOffsetY(220);

        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/tiktok.png'))
            ->setHeight(60)
            ->setWidth(60)
            ->setOffsetX(663)
            ->setOffsetY(105);

        // ************************* Add the fourth slide *****************************
        $imagePath = public_path("/presentation/static/bg") . "/bg2.JPG";
        $fourthSlide = $presentation->createSlide();

        // Set the slide background color (optional)
        $backgroundImage = $fourthSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($imageWidth);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // insert img
        $inserimgPath = public_path("/presentation/static") . "/insertimg1.png";
        $backgroundImage = $fourthSlide->createDrawingShape();
        $backgroundImage->setPath($inserimgPath);
        $backgroundImage->setWidth($imageWidth / 3);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX($imageWidth / 8);
        $backgroundImage->setOffsetY(0);

        $textShape =  $fourthSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(450);
        $textShape->setOffsetX(500);
        $textShape->setOffsetY(110);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('การค้นหาจากชื่อผลิตภัณฑ์');
        $textRun->getFont()->setSize(30);
        $textRun->getFont()->setColor(new Color('FFFF00'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName('Times New Roman');

        $textShape =  $fourthSlide->createRichTextShape();
        $textShape->setHeight(300);
        $textShape->setWidth(400);
        $textShape->setOffsetX(520);
        $textShape->setOffsetY(200);
        $textShape->getBorder()->setColor(new Color('FFFF00'))->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_SINGLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature');
        $textRun->getFont()->setSize(15);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName('Times New Roman');

        // ************************* Add the fifth slide *****************************
        $imagePath = public_path("/presentation/static/bg") . "/bg2.JPG";
        $fifthSlide = $presentation->createSlide();

        // Set the slide background color (optional)

        $backgroundImage = $fifthSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($imageWidth);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // first component
        $textShapeWidth = 500;
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(120);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(20);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Top performance');
        $textRun->getFont()->setSize(48);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName('Berlin Sans FB');

        // Add shadow effect to the text shape
        $textShape->getShadow()->setVisible(true);
        $textShape->getShadow()->setDistance(6); // Distance of shadow from shape
        $textShape->getShadow()->setBlurRadius(5); // Blur radius of shadow
        $textShape->getShadow()->setColor(new Color('E11616')); // Color of shadow
        // second component
        $textShapeWidth = 250;
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(120);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Follower 1M (10acc)');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(200);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('737373'))->setEndColor(new Color('737373'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('view')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/view.png'))
            ->setHeight(75)
            ->setWidth(75)
            ->setOffsetX(112)
            ->setOffsetY(200);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(167);
        $textShape->setOffsetY(215);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('view');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(280);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('cd6966'))->setEndColor(new Color('cd6966'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('like')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/like.png'))
            ->setHeight(75)
            ->setWidth(75)
            ->setOffsetX(112)
            ->setOffsetY(280);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(167);
        $textShape->setOffsetY(295);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('like');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(360);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('65990b'))->setEndColor(new Color('65990b'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('comment')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/comment.png'))
            ->setHeight(75)
            ->setWidth(75)
            ->setOffsetX(112)
            ->setOffsetY(360);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(130);
        $textShape->setOffsetX(177);
        $textShape->setOffsetY(375);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('comment');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(440);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('share')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/share.png'))
            ->setHeight(75)
            ->setWidth(75)
            ->setOffsetX(112)
            ->setOffsetY(440);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(167);
        $textShape->setOffsetY(455);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('share');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Influencer
        // Add dynamic content to the first slide
        $textShape =  $fifthSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX(708);
        $textShape->setOffsetY(180);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        // Influencer
        // Add dynamic content to the first slide
        $textShape =  $fifthSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX(708);
        $textShape->setOffsetY(180);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Influencer3');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/influ3.jpg'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(656)
            ->setOffsetY(270);

        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/tiktok.png'))
            ->setHeight(60)
            ->setWidth(60)
            ->setOffsetX(663)
            ->setOffsetY(175);

        // Save the presentation
        // $dynamicPresentationPath = public_path("/presentation/result") . "/sample.pptx";
        // $objWriter = IOFactory::createWriter($presentation, 'PowerPoint2007');
        // $objWriter->save($dynamicPresentationPath);
    }
}
