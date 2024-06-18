<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Influencer;
use Barryvdh\DomPDF\Facade\Pdf;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Date;
use Mpdf\Mpdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;
use App\Utilities\PdfFiller;

class PdfController extends Controller
{

    public function formatThaiIdCard($idCard)
    {
        $Space = '&nbsp;&nbsp;&nbsp;'; // Two spaces
        return substr($idCard, 0, 1) . $Space .
            substr($idCard, 1, 4) . $Space .
            substr($idCard, 5, 5) . $Space .
            substr($idCard, 10, 2) . $Space .
            substr($idCard, 12, 1);
    }
    public function fillPdfForm(Request $request)
    {
        $id = $request->influencer_id;
        $influencer = Influencer::find($id);

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
        ]);

        $formattedIdCard = $this->formatThaiIdCard($influencer->id_card);
        $company_name = "บริษัทโฆษณา Gorillaideas";
        $address = "1792 ถ. ประชาสงเคราะห์ แขวงดินแดง ดินแดง กรุงเทพมหานคร 10400";
        // background-color:red;
        $content = array(
            '
            <div style="position:absolute; top:135px; left:150px; width:650px; font-size:16px;"> ' . $company_name . '  </div>
            <div style="position:absolute; top:165px; left:150px; width:650px; font-size:16px;"> ' . $address . '  </div>
            <div style="position:absolute; top:193px; left:502px; width:650px; font-size:28px;"> ' . $formattedIdCard . '  </div>
            <div style="position:absolute; top:230px; left:150px; width:650px; font-size:16px;"> ' . $influencer->fullname . '  </div>
            <div style="position:absolute; top:265px; left:150px; width:650px; font-size:16px;"> ' . $influencer->address_of_card . '  </div>
            <div style="position:absolute; top:290px; left:635px; width:650px; font-size:28px;"> ' . '/' . '  </div>
        ',
        );

        // return $influencer;
        $mpdf->setSourceFile(public_path('pdf/lastest.pdf'));
        $mpdf->AddPage('');
        $import_page = $mpdf->ImportPage(1);
        $mpdf->UseTemplate($import_page);
        $mpdf->WriteHTML($content[0]);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="tax_book_fifty.pdf"',
            'Content-Transfer-Encoding' => 'binary',
        ];
        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, $headers);
    }
    public function generatePdf()
    {
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
        // Retrieve influencer details
        $influencer = Influencer::find($request->influencer_id);

        // PDF Configuration
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path() . '/custom/font/directory',
            ]),
            'fontdata' => $fontData + [
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
        ]);

        $currentDate = date('d/m/Y');
        $mpdf->SetTitle('หนังสือรับรองการหักภาษี ณ ที่จ่าย ' . $currentDate);
        $mpdf->AddPage();

        // Assuming these details are retrieved from your database or form input
        $issuerName = 'บริษัท ตัวอย่าง จำกัด';
        $issuerAddress = '257/1 ถนนสีลม แขวงสีลม เขตบางรัก กรุงเทพมหานคร 10240';
        $issuerTaxId = '3103661100';

        $recipientName = 'บริษัท แกนน์ อินดัสตรี จำกัด';
        $recipientAddress = '259/83 ซ. พิพัฒน์ 2 คลองสาน เขตบางกอกน้อย กรุงเทพมหานคร 10110';
        $recipientTaxId = '184150003375';

        $paymentDate = '11/02/2553';
        $amountPaid = '10,000.00';
        $taxWithheld = '300.00';

        $checked = '☑';
        $unchecked = '☐';

        $html = '
            <div style="text-align: left; font-size: 14px; margin-top: 5px;">
                   <b> ฉบับที่ 1 </b> สำหรับผู้ถูกหักภาษี ณ ที่จ่ายใช้แนบพร้อมกับแบบแสดงรายการภาษี
            </div>
            <div style="text-align: left; font-size: 14px; margin-bottom: 10px;">
                    <b> ฉบับที่ 2 </b> สำหรับผู้ถูกหักภาษี ณ ที่จ่ายเก็บไว้เป็นหลักฐาน
            </div>
            <div style="border: 1px solid black; padding: 10px; margin-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="text-align: center; flex-grow: 1;">
                    <div style="font-size: 22px; padding-bottom: 5px;">
                        <b>หนังสือรับรองการหักภาษี ณ ที่จ่าย</b>
                    </div>
                    <div style="font-size: 18px;">
                        ตามมาตรา 50 ทวิ แห่งประมวลรัชฎากร
                    </div>
                </div>
                <div style="flex-shrink: 0; text-align: right;">
                    <div style="font-size: 18px;">เลขที่: ..............................</div>
                    <div style="font-size: 18px;">เล่มที่: ..............................</div>
                </div>
            </div>
        
                <div style="border: 1px solid black; padding: 10px; margin-top: 20px;">
                    <div style="font-size: 18px;"><b>ผู้มีหน้าที่หักภาษี ณ ที่จ่าย:</b></div>
                    <div>ชื่อ: ' . $issuerName . '</div>
                    <div>ที่อยู่: ' . $issuerAddress . '</div>
                    <div>เลขประจำตัวผู้เสียภาษีอากร: ' . $issuerTaxId . '</div>
                </div>
        
                <div style="border: 1px solid black; padding: 10px; margin-top: 10px;">
                    <div style="font-size: 18px;"><b>ผู้ถูกหักภาษี ณ ที่จ่าย:</b></div>
                    <div>ชื่อ: ' . $recipientName . '</div>
                    <div>ที่อยู่: ' . $recipientAddress . '</div>
                    <div>เลขประจำตัวผู้เสียภาษีอากร: ' . $recipientTaxId . '</div>
        
                    <div style="font-size: 18px;">
                        <div>ลำดับที่: <span style="border-bottom: 1px dotted black; width: 100px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                        <div>ในแบบ: <span style="border-bottom: 1px dotted black; width: 100px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                        <div>' . $unchecked . ' (1) ภ.ง.ด.1ก ' . $unchecked . ' (2) ภ.ง.ด.1ก พิเศษ ' . $unchecked . ' (3) ภ.ง.ด.2 ' . $checked . ' (4) ภ.ง.ด.3</div>
                        <div>' . $unchecked . ' (5) ภ.ง.ด.2ก ' . $unchecked . ' (6) ภ.ง.ด.3ก ' . $unchecked . ' (7) ภ.ง.ด.53</div>
                    </div>
                </div>
        
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                        font-size: 16px;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 8px;
                        text-align: left;
                    }
                    .center {
                        text-align: center;
                    }
                </style>
        
                <table>
                    <thead>
                        <tr>
                            <th>ประเภทเงินได้ที่จ่าย</th>
                            <th>วัน เดือน ปี ที่จ่าย</th>
                            <th>จำนวนเงินที่จ่าย</th>
                            <th>ภาษีที่หักและนำส่งไว้</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ค่าบริการ</td>
                            <td class="center">' . $paymentDate . '</td>
                            <td class="center">' . $amountPaid . '</td>
                            <td class="center">' . $taxWithheld . '</td>
                        </tr>
                        <tr>
                            <td colspan="2">รวมเงินที่จ่ายและภาษีที่หักจ่าย</td>
                            <td class="center">' . $amountPaid . '</td>
                            <td class="center">' . $taxWithheld . '</td>
                        </tr>
                    </tbody>
                </table>
        
                <div style="border: 1px solid black; padding: 10px; margin-top: 10px;">
                    <div style="font-size: 18px;">
                        <div>รวมเงินภาษีที่หักและนำส่ง: <span style="border-bottom: 1px dotted black; width: 100px;">' . $taxWithheld . '</span></div>
                        <div>ขอรับรองว่าข้อความและตัวเลขดังกล่าวข้างต้นถูกต้องตรงกับความจริงทุกประการ</div>
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <p>ลงชื่อ ................................................................. ผู้มีหน้าที่หักภาษี ณ ที่จ่าย</p>
                        <p>ชื่อ: ' . $issuerName . '</p>
                        <p>วันที่: ' . $currentDate . '</p>
                    </div>
                </div>
        
                <div style="font-size: 18px;">หมายเหตุ: เลขประจำตัวผู้เสียภาษีอากร (13 หลัก) หมายถึง</div>
                <ol style="font-size: 16px;">
                    <li>เลขประจำตัวผู้เสียภาษีของกรมสรรพากร</li>
                    <li>เลขบัตรประชาชนในกรณีเป็นบุคคลธรรมดา</li>
                    <li>เลขที่ของสาขาของนิติบุคคลในกรณีเป็นสถานประกอบการย่อย</li>
                </ol>
            </div>
        ';

        $mpdf->WriteHTML($html);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="tax_book_fifty.pdf"',
            'Content-Transfer-Encoding' => 'binary',
        ];
        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, $headers);
    }
    public function showPdfForm(Request $request)
    {
        $modelData = Influencer::find($request->influencer_id);
        // Pass the data to the view
        return view('pdf_form', ['modelData' => $modelData]);
    }
}
