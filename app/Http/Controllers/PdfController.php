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
}
