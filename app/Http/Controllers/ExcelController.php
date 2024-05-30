<?php

namespace App\Http\Controllers;

use App\Exports\ServiceCenterExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductTimeline;
use App\Http\Controllers\Controller;

class ExcelController extends Controller
{
    public function ExportServiceCenterByComp($id)
    {
        try {
            return Excel::download(new ServiceCenterExport($id), 'สรุปภาพรวมเข้ารับบริการ.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error downloading file: ' . $e->getMessage()], 400);
        }
    }
}


