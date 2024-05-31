<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\ProductTimeline;

class ServiceCenterExport implements FromView
{
    protected $id;
    protected $month;
    protected $year;

    public function __construct($month, $id, $year)
    {
        $this->month = $month;
        $this->year = $year;
        $this->id = $id;
    }

    public function view(): View
    {
        $data = ProductTimeline::with(['product_items.project_timelines'])
            ->where('project_id', $this->id)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->get();
        return view('Exceldata', [
            'data' => $data
        ]);
    }
}
