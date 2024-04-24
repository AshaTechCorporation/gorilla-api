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


class PresentationController extends Controller
{
    public function generatePresentation()
    {
        // Create a new presentation
        $presentation = new PhpPresentation();

        $presentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);

        //  ************************* Add the first slide *************************
        $firstSlide = $presentation->getActiveSlide();

        // Set the slide background color (optional)
        // your image file
        $imagePath = public_path("/presentation/static/bg") . "/thumbnail.png";
        $imageWidth = 960;
        $imageHeight = 540;
        $defaultFontName = 'Calibri'; // Use a common font
        // Set the background image for the first slide
        $backgroundImage = $firstSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($imageWidth);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // first component
        $textShapeWidth = 350;
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

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
        $textRun->getFont()->setName($defaultFontName);


        // second component
        $textShapeWidth = 500; // Width of the text shape
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2; // Calculate the offset to center horizontally

        // Add dynamic content to the first slide
        $textShape = $firstSlide->createRichTextShape();
        $textShape->setHeight(150);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(200);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('ProductName');
        $textRun->getFont()->setSize(52);
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setColor(new Color('FFFFFF'));
        $textRun->getFont()->setName($defaultFontName);
        
        // Add shadow effect to the text shape
        $textShape->getShadow()->setVisible(true);
        $textShape->getShadow()->setDirection(180); // Angle of shadow (in degrees)
        $textShape->getShadow()->setDistance(8); // Distance of shadow from shape
        $textShape->getShadow()->setBlurRadius(2); // Blur radius of shadow
        $textShape->getShadow()->setColor(new Color('FFE06B20')); // Color of shadow

        // third component
        $textShapeWidth = 250; // Width of the text shape
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2; // Calculate the offset to center horizontally

        // Add dynamic content to the first slide
        $textShape = $firstSlide->createRichTextShape();
        $textShape->setHeight(100);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(300);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun(Date::now()->format('Y-m-d H:i:s'));
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $textRun->getFont()->setName($defaultFontName);

        //  *************************Add the second slide *************************
        $secondSlide = $presentation->createSlide();

        // Set the slide background color (optional)

        $backgroundImage = $secondSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($imageWidth);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // first component
        $textShapeWidth = 500;
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

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
        $textRun->getFont()->setColor(new Color('00000'));
        $textRun->getFont()->setName($defaultFontName);

        // second component
        $textShapeWidth = 250;
        $textShapeOffsetX = ($imageWidth - $textShapeWidth) / 2;

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(100);
        $textShape->setWidth($textShapeWidth);
        $textShape->setOffsetX($textShapeOffsetX);
        $textShape->setOffsetY(120);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Overall');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new Color('00000'));
        $textRun->getFont()->setName($defaultFontName);

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
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(112);
        $textShape->setOffsetY(420);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

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
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(324);
        $textShape->setOffsetY(420);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

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
        $textShape->setWidth(100);
        $textShape->setOffsetX(536);
        $textShape->setOffsetY(320);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('comment');
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(536);
        $textShape->setOffsetY(420);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

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
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // Add dynamic content to the first slide
        $textShape = $secondSlide->createRichTextShape();
        $textShape->setHeight(60);
        $textShape->setWidth(100);
        $textShape->setOffsetX(748);
        $textShape->setOffsetY(420);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('123');
        $textRun->getFont()->setSize(14);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        // ************************* Add the third slide *****************************
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
        $textShape->setHeight(100);
        $textShape->setWidth(150);
        $textShape->setOffsetX(126);
        $textShape->setOffsetY(110);
        $textShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add a text run to the shape
        $textRun = $textShape->createTextRun('Influencer1');
        $textRun->getFont()->setSize(20);
        $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));
        $textRun->getFont()->setBold(true);
        $textRun->getFont()->setName($defaultFontName);

        $shape = $thirdSlide->createDrawingShape();
        $shape->setName('image')
            ->setDescription('My image description')
            ->setPath(public_path('/presentation/static/temp.png'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(52)
            ->setOffsetY(220);

        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        // Influencer 2
        // Add dynamic content to the first slide
        $textShape =  $thirdSlide->createRichTextShape();
        $textShape->setHeight(100);
        $textShape->setWidth(150);
        $textShape->setOffsetX(402);
        $textShape->setOffsetY(110);
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
            ->setPath(public_path('/presentation/static/temp.png'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(354)
            ->setOffsetY(220);


        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');
        // Influencer 3
        // Add dynamic content to the first slide
        $textShape =  $thirdSlide->createRichTextShape();
        $textShape->setHeight(100);
        $textShape->setWidth(150);
        $textShape->setOffsetX(678);
        $textShape->setOffsetY(110);
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
            ->setPath(public_path('/presentation/static/temp.png'))
            ->setHeight(250)
            ->setWidth(250)
            ->setOffsetX(656)
            ->setOffsetY(220);


        $shape->getHyperlink()->setUrl('https://www.tiktok.com/@tumkomsun/video/7336527336768113922');

        // ************************* Add the fourth slide *****************************

        $fourthSlide = $presentation->createSlide();

        // Set the slide background color (optional)

        $backgroundImage = $fourthSlide->createDrawingShape();
        $backgroundImage->setPath($imagePath);
        $backgroundImage->setWidth($imageWidth);
        $backgroundImage->setHeight($imageHeight);
        $backgroundImage->setOffsetX(0);
        $backgroundImage->setOffsetY(0);

        // Add dynamic content to the third slide
        $textShape = $fourthSlide->createRichTextShape();
        $textShape->setHeight(300);
        $textShape->setWidth(600);
        $textShape->setOffsetX(180);
        $textShape->setOffsetY(150);

        // // Add a text run to the shape
        // $textRun = $textShape->createTextRun('Dynamic Text on the fourth Slide');
        // $textRun->getFont()->setSize(48);
        // $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('00000'));


        // Add dynamic content to the fourth slide and continue the process for more slides...

        // Save the presentation
        $dynamicPresentationPath = public_path("/presentation/result") . "/sample.pptx";
        $objWriter = IOFactory::createWriter($presentation, 'PowerPoint2007');
        $objWriter->save($dynamicPresentationPath);
    }
}