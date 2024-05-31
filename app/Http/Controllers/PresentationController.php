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

use App\Models\Presentation;
use App\Models\Project;
use Illuminate\Support\Facades\DB;


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
    public function Thumbnail($name)
    {
        $imagePath = public_path(Presentation::where('Template_Name', 'Thumbnail')
            ->value('Background_Template'));

        //  ************************* Add the first slide *************************
        $firstSlide = $this->presentation->getActiveSlide();

        // $imagePath = public_path("/presentation/static/bg") . "/thumbnail.png";

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
        $textRun = $textShape->createTextRun($name);
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
        $secondSlide = $this->presentation->createSlide();

        $imagePath = public_path(Presentation::where('Template_Name', 'Status')
            ->value('Background_Template'));
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

    public function Influ_format_3($influencer)
    {
        // ************************* Add the third slide *****************************
        $imagePath = public_path(Presentation::where('Template_Name', 'Influ_format_3')
            ->value('Background_Template'));

        $thirdSlide = $this->presentation->createSlide();

        // Set the slide background color (optional)
        $backgroundImage = $thirdSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
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
        $textRun = $textShape->createTextRun($influencer[0]->fullname);
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun = $textShape->createTextRun($influencer[1]->fullname);
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun = $textShape->createTextRun($influencer[2]->fullname);
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
    }
    public function Influ_format_2($influencer)
    {
        // ************************* Add the third slide *****************************
        $imagePath = public_path(Presentation::where('Template_Name', 'Influ_format_3')
            ->value('Background_Template'));

        $thirdSlide = $this->presentation->createSlide();
        $offxcom1 = 276;
        $offycom1 = 110;
        // Set the slide background color (optional)
        $backgroundImage = $thirdSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // Influencer 1
        // Add dynamic content to the first slide
        $textShape =  $thirdSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX($offxcom1);
        $textShape->setOffsetY($offycom1);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun($influencer[0]->fullname);
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/influ1.jpg'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX($offxcom1 - 74)
            ->setOffsetY($offycom1 + 110);

        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/tiktok.png'))
            ->setHeight(60)
            ->setWidth(60)
            ->setOffsetX($offxcom1 - 45)
            ->setOffsetY($offycom1 - 5);

        // Influencer 2
        $offxcom2 = 552;
        $offycom2 = 110;

        // Text shape
        $textShape = $thirdSlide->createRichTextShape();
        $textShape->setHeight(50);
        $textShape->setWidth(150);
        $textShape->setOffsetX($offxcom2);
        $textShape->setOffsetY($offycom2);
        $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getBorder()->setColor(new Color('00000'))->setLineWidth(5)->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_DOUBLE);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Text run
        $textRun = $textShape->createTextRun($influencer[1]->fullname);
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

        // Image shape
        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/influ2.jpg'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX($offxcom2 - 74)
            ->setOffsetY($offycom2 + 110);

        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        // TikTok logo
        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/tiktok.png'))
            ->setHeight(60)
            ->setWidth(60)
            ->setOffsetX($offxcom2 - 45)
            ->setOffsetY($offycom2 - 5);
    }
    public function Influ_format_1($influencer)
    {
        // ************************* Add the third slide *****************************
        $imagePath = public_path(Presentation::where('Template_Name', 'Influ_format_3')
            ->value('Background_Template'));

        $thirdSlide = $this->presentation->createSlide();

        // Set the slide background color (optional)
        $backgroundImage = $thirdSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

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
        $textRun = $textShape->createTextRun($influencer[0]->fullname);
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
    }
    public function Insertdata()
    {
        // ************************* Add the fourth slide *****************************
        $imagePath = public_path(Presentation::where('Template_Name', 'Insertdata')
            ->value('Background_Template'));
        // $imagePath = public_path("/presentation/static/bg") . "/bg2.JPG";
        $fourthSlide = $this->presentation->createSlide();

        // Set the slide background color (optional)
        $backgroundImage = $fourthSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // insert img
        $inserimgPath = public_path("/presentation/static") . "/insertimg1.png";
        $backgroundImage = $fourthSlide->createDrawingShape();
        $backgroundImage->setPath($inserimgPath);
        $backgroundImage->setWidth($this->imageWidth / 3);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX($this->imageWidth / 8);
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
    }
    public function Topperform()
    {
        // ************************* Add the fifth slide *****************************
        $imagePath = public_path(Presentation::where('Template_Name', 'Topperform')
            ->value('Background_Template'));
        // $imagePath = public_path("/presentation/static/bg") . "/bg2.JPG";
        $fifthSlide = $this->presentation->createSlide();

        // Set the slide background color (optional)

        $backgroundImage = $fifthSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($this->imageWidth);
        $backgroundImage->setHeight($this->imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // first component
        $textShapeWidth = 500;
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2;

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
        $textShapeOffsetX = ($this->imageWidth - $textShapeWidth) / 2;

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
        $textRun->getFont()->setName($this->defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('bgview')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/textbox/1.png'))
            ->setHeight(75)
            ->setWidth(420)
            ->setOffsetX(102)
            ->setOffsetY(200);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(200);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('737373'))->setEndColor(new Color('737373'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun->getFont()->setName($this->defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('bglike')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/textbox/2.png'))
            ->setHeight(75)
            ->setWidth(420)
            ->setOffsetX(102)
            ->setOffsetY(280);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(280);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('cd6966'))->setEndColor(new Color('cd6966'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun->getFont()->setName($this->defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('commentbg')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/textbox/3.png'))
            ->setHeight(75)
            ->setWidth(420)
            ->setOffsetX(102)
            ->setOffsetY(360);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(360);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('65990b'))->setEndColor(new Color('65990b'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun->getFont()->setName($this->defaultFontName);

        $shape = $fifthSlide->createDrawingShape();
        $shape->setName('share')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/textbox/4.png'))
            ->setHeight(75)
            ->setWidth(420)
            ->setOffsetX(102)
            ->setOffsetY(440);

        // Add dynamic content to the first slide
        $textShape = $fifthSlide->createRichTextShape();
        $textShape->setHeight(75);
        $textShape->setWidth(400);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(440);
        // $textShape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(45)->setStartColor(new Color('fcba04'))->setEndColor(new Color('fcba04'));
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun->getFont()->setName($this->defaultFontName);

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
        $textRun->getFont()->setName($this->defaultFontName);

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
    }

    public function Title()
    {
        $imagePath = public_path(Presentation::where('Template_Name', 'Thumbnail')
            ->value('Background_Template'));

        //  ************************* Add the first slide *************************
        $firstSlide = $this->presentation->createSlide();

        // $imagePath = public_path("/presentation/static/bg") . "/thumbnail.png";

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

    public function generatePresentation($id)
    {
        $project = Project::with('products.product_items.project_timelines')->find($id);

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $project);
        $this->Thumbnail($project->name);
        $this->Status();
        $this->createInfluSide($project->influencers);
        $this->Insertdata();
        $this->Topperform();
        $this->Title();
        // Save the presentation
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");

        $objWriter = IOFactory::createWriter($this->presentation, 'PowerPoint2007');
        return $objWriter->save('php://output');
    }
    public function createInfluSide($influencers)
    {   
        $chunkSize = 3;
        $chunkIndex = 0;
        $chunks = collect();
    
        foreach ($influencers as $index => $influencer) {
            if ($index % $chunkSize === 0) {
                $chunks->push(collect()); 
                $chunkIndex++; 
            }
            $chunks[$chunkIndex - 1]->push($influencer); 
        }
    
        foreach ($chunks as $chunk) {
            $chunkCount = $chunk->count();
            if ($chunkCount === 3) {
                $this->Influ_format_3($chunk);
            } elseif ($chunkCount === 2) {
                $this->Influ_format_2($chunk);
            } elseif ($chunkCount === 1) {
                $this->Influ_format_1($chunk);
            }
        }
    }

    public function getList()
    {
        $Item = Presentation::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;


        $col = array('id', 'Template_Name', 'Background_Template');

        $orderby = array('id', 'Template_Name', 'Background_Template');

        $D = Presentation::select($col);

        if ($orderby[$order[0]['column']]) {
            $D->orderby($orderby[$order[0]['column']], $order[0]['dir']);
        }

        if ($search['value'] != '' && $search['value'] != null) {

            $D->Where(function ($query) use ($search, $col) {

                //search datatable
                $query->orWhere(function ($query) use ($search, $col) {
                    foreach ($col as &$c) {
                        $query->orWhere($c, 'like', '%' . $search['value'] . '%');
                    }
                });

                //search with
                // $query = $this->withPermission($query, $search);
            });
        }

        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);

            for ($i = 0; $i < count($d); $i++) {

                $No = $No + 1;
                $d[$i]->No = $No;
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $Item = new Presentation();
            $Item->Template_Name = $request->name;
            $Item->Background_Template = $request->background;

            $Item->save();
            //

            //log
            $userId = "admin";
            $type = 'เพิ่มsocial';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);


            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Presentation  $Presentation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checkId = Presentation::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }
        $Item = Presentation::where('id', $id)
            ->first();
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Presentation  $Presentation
     * @return \Illuminate\Http\Response
     */
    public function edit(Presentation $Presentation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Presentation  $Presentation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $Item = Presentation::find($id);
            $Item->Template_Name = $request->Template_Name;
            $Item->Background_Template = $request->Background_Template;

            $Item->save();
            //

            //log
            $userId = "admin";
            $type = 'แก้ไขsocial';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);


            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Presentation  $Presentation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Presentation::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบsocial';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnUpdate('ดำเนินการสำเร็จ');
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }
}
