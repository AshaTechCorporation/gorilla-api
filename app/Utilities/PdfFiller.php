<?php

namespace App\Utilities;

use TCPDF;

class PdfFiller
{
    public function fillForm($formPath, $data)
    {
        // Load the existing PDF file
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pageCount = $pdf->loadFile($formPath);

        // Import all pages from the existing PDF
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);

            // Fill the form fields
            foreach ($data as $name => $value) {
                $pdf->SetXY(10, 10); // Adjust these coordinates as needed
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Write(0, $name . ': ' . $value);
            }
        }

        // Save the filled PDF file
        $filledFormPath = public_path('filled_form.pdf');
        $pdf->Output($filledFormPath, 'F');

        return $filledFormPath;
    }
}