<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\ProductTimeline;

class ServiceCenterExport implements FromView
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $data = ProductTimeline::with(['product_items.project_timelines'])
            ->where('id', $this->id)
            ->get();
        return view('Exceldata', [
            'data' => $data
        ]);
    }
}

