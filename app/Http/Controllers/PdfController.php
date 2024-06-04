<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generatePdf(){
        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y'), 
        ];
    
        // Load the view
        $pdf = PDF::loadView('pdf_format.power_point', $data);
    
        // Set the paper size to match PowerPoint slide (16:9 aspect ratio)
        $pdf->setPaper([16 * 72, 9 * 72], 'landscape');
    
        // Download the PDF
        return $pdf->download('powerpoint.pdf');
    }

    public function tax_book_fifty(Request $request) 
    {


        //PDF
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path() . '/custom/font/directory',
            ]),
            'fontdata' => $fontData + [ // lowercase letters only in font key
                'th-sarabun-it' => [
                    'R' => 'THSarabunIT๙.ttf',
                    'I' => 'THSarabunIT๙ Italic.ttf',
                    'B' => 'THSarabunIT๙ Bold.ttf',
                    'BI' => 'THSarabunIT๙ BoldItalic.ttf',
                ],
            ],
            'default_font' => 'th-sarabun-it',
            'mode' => 'utf-8',
            'format' => 'A4',
            // 'default_font_size' => 12,
            // 'default_font' => 'sarabun',
            // 'margin_left' => 5,
            // 'margin_right' => 5,
            // 'margin_top' => 5,
            // 'margin_bottom' => 5,
            // 'margin_header' => 5,
            // 'margin_footer' => 5,
        ]);

        $mpdf->SetTitle('หนังสือรับรองการหักภาษี');
        $mpdf->AddPage();
        $html = '
            <div style="text-align: center; font-size: 22px; padding-bottom: 5px; "><b> หนังสือรับรองการหักภาษี ณ ที่จ่าย ตามมาตรา 50 ทวิ แห่งประมวลรัชฎากร </div>
            <div style="text-align: left; font-size: 18px;"> ผู้มีหน้าที่หักภาษี ณ ที่จ่าย : </div>
            <div style="text-align: left; font-size: 18px;"> ชื่อสถาบันมะเร็งแห่งชาติ </div>
            <div style="text-align: left; font-size: 18px;"> ที่อยู่ 268/1 ถนนพระรามหก แขวงทุงพญาไท เขตราชเทวี กรุงเทพฯ 10400 </div>

            <div style="text-align: left; font-size: 18px; padding-top: 10px;"> ผู้มีหน้าที่หักภาษี ณ ที่จ่าย : </div>
            <div style="text-align: left; font-size: 18px;"> ชื่อสถาบันมะเร็งแห่งชาติ </div>
            <div style="text-align: left; font-size: 18px; padding-bottom: 5px;"> ที่อยู่ 268/1 ถนนพระรามหก แขวงทุงพญาไท เขตราชเทวี กรุงเทพฯ 10400 </div>

            <div style="position:absolute; top: 100; left: 88px; width: 650px; text-align: right; font-size:16px;"> เลขประจำตัวผู้เสียภาษีอากร </div>
            <div style="position:absolute; top: 120; left: 88px; width: 650px; text-align: right; font-size:16px;"> เลขประจำตัวผู้เสียภาษีอากร </div>

            <div style="position:absolute; top: 150; left: 88px; width: 650px; text-align: right; font-size:16px;"> เลขประจำตัวผู้เสียภาษีอากร </div>
            <div style="position:absolute; top: 170; left: 88px; width: 650px; text-align: right; font-size:16px;"> เลขประจำตัวผู้เสียภาษีอากร </div>

            <style>
                table {
                    width:100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid black;
                }
            </style>

            <table>
                <tr>
                    <th> ประเภทเงินได้ที่จ่าย </th>
                    <th> วัน เดือน ปี ที่จ่าย </th>
                    <th> จำนวนเงินที่จ่าย </th>
                    <th> ภาษีที่หักและนำส่งไว้ </th>
                </tr>
                <tr>
                    <td style="border-top: none; border-bottom: none;"> &nbsp; </td>
                    <td style="border-top: none; border-bottom: none;"> </td>
                    <td style="border-top: none; border-bottom: none;"> </td>
                    <td style="border-top: none; border-bottom: none;"> </td>
                </tr>
                <tr>
                    <td style="border-top: none;"> &nbsp; </td>
                    <td style="border-top: none;"> </td>
                    <td style="border-top: none; border-bottom: none;"> </td>
                    <td style="border-top: none; border-bottom: none;"> </td>
                </tr>
                <tr>
                    <td style="border: none;" colspan="2"> รวมเงินที่จ่ายและภาษีที่หักจ่าย </td>
                    <td> </td>
                    <td> </td>
                </tr>
            </table>

            <div style="position:absolute; left: 60px; width: 650px; text-align: left; font-size:16px; top: 323;"> รวมภาษีที่หักและนำส่ง </div>
            <div style="position:absolute; left: 60px; width: 650px; text-align: left; font-size:16px; top: 342;"> ผู้จ่ายเงิน </div>
            <div style="position:absolute; left: 200px; width: 650px; text-align: left; font-size:16px; top: 342;"> ออกให้ครั้งเดียว </div>
            <div style="position:absolute; left: 400px; width: 650px; text-align: left; font-size:16px; top: 342;"> ออกภาษีให้ตลอดไป </div>
            <div style="position:absolute; left: 600px; width: 650px; text-align: left; font-size:16px; top: 342;"> /หักภาษี ณ ที่จ่าย </div>

            <table style="margin-top: 40px; border-collapse: collapse; border: 1px solid black;">
                <tr>
                    <td style="text-align: center; border: none; padding-top: 5px;"> ขอรับรองว่าข้อความและตัวเลขดังกล่าวข้างต้นถูกต้องตรงกับความจริงทุกประการ </td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ลงชื่อ ....................................................................................... ผู้มีหน้าที่หักภาษี ณ ที่จ่าย</td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none;"> ชื่อ </td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none; padding-bottom: 5px;"> วันที่ </td>
                </tr>
            </table>

        ';

        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
