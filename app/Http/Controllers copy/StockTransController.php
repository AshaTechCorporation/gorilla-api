<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\ProductUnit;
use App\Models\StockTrans;
use App\Models\StockTransLine;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StockTransController extends Controller
{
    public function getList()
    {
        $Item = StockTrans::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;

        $type = $request->type;

        $col = array('id', 'code', 'date', 'order_id', 'source', 'status', 'remark', 'type', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'code', 'date', 'order_id', 'source', 'status', 'remark', 'type', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = StockTrans::select($col);


        if (isset($type)) {
            $D->where('type', $type);
        }

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
                $d[$i]->lines = StockTransLine::where('inout_id', $d[$i]->id)->get();
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
        $loginBy = $request->login_by;

        DB::beginTransaction();

        try {
            $prefix = "#OR-";
            $id = IdGenerator::generate(['table' => 'stock_trans', 'field' => 'code', 'length' => 9, 'prefix' => $prefix]);

            $Item = new StockTrans();
            $Item->code = $id;
            $Item->order_id = $request->order_id;
            $Item->remark = $request->remark;
            $Item->type = $request->type;
            $Item->date = $request->date;
            $Item->source = $request->source;

            $Item->save();


            foreach ($request->products as $key => $value) {

                $checkStock = Products::find($value['product_id']);
                if ($checkStock) {
                    if ($request->type == "OUT") {
                        if ($checkStock->qty < $value['qty']) {

                            $ItemF = new StockTransLine();
                            $ItemF->inout_id = $Item->id;
                            $ItemF->product_id = $value['product_id'];
                            $ItemF->qty = $value['qty'];
                            $ItemF->unit_id = $value['unit_id'];
                            $ItemF->save();
                        } else {
                            return $this->returnErrorData('สินค้ามีไม่พอเบิกออก ', 404);
                        }
                    } else {
                        $ItemF = new StockTransLine();
                        $ItemF->inout_id = $Item->id;
                        $ItemF->product_id = $value['product_id'];
                        $ItemF->qty = $value['qty'];
                        $ItemF->unit_id = $value['unit_id'];
                        $ItemF->save();
                    }
                }
            }

            //

            //log
            $userId = "admin";
            $type = 'เพิ่มรายการ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $request->name;
            $this->Log($userId, $description, $type);
            //

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
     * @param  \App\Models\StockTrans  $stockTrans
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = StockTrans::where('id', $id)
            ->first();


        if (!empty($Item)) {

            $Item['stock_lines'] = StockTransLine::where('inout_id',  $id)->get();
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StockTrans  $stockTrans
     * @return \Illuminate\Http\Response
     */
    public function edit(StockTrans $stockTrans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StockTrans  $stockTrans
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loginBy = $request->login_by;

        if (!isset($id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = StockTrans::find($id);
            $Item->order_id = $request->order_id;
            $Item->remark = $request->remark;
            $Item->type = $request->type;
            $Item->date = $request->date;
            $Item->source = $request->source;

            $Item->save();

            StockTransLine::where('inout_id', $id)->delete();


            foreach ($request->products as $key => $value) {

                $checkStock = Products::find($value['product_id']);
                if ($checkStock) {
                    if ($request->type == "OUT") {
                        if ($checkStock->qty < $value['qty']) {

                            $ItemF = new StockTransLine();
                            $ItemF->inout_id = $Item->id;
                            $ItemF->product_id = $value['product_id'];
                            $ItemF->qty = $value['qty'];
                            $ItemF->unit_id = $value['unit_id'];
                            $ItemF->save();
                        } else {
                            return $this->returnErrorData('สินค้ามีไม่พอเบิกออก ', 404);
                        }
                    } else {
                        $ItemF = new StockTransLine();
                        $ItemF->inout_id = $Item->id;
                        $ItemF->product_id = $value['product_id'];
                        $ItemF->qty = $value['qty'];
                        $ItemF->unit_id = $value['unit_id'];
                        $ItemF->save();
                    }
                }
            }
            //

            //log
            $userId = "admin";
            $type = 'แก้ไขรายการ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $request->name;
            $this->Log($userId, $description, $type);
            //

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
     * @param  \App\Models\StockTrans  $stockTrans
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = StockTrans::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบรายการผลิต';
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

    public function exportPDF($id)
    {

        $Item = StockTrans::find($id);

        if ($Item) {
            $Item->lines = StockTransLine::where('inout_id', $Item->id)->get();

            //PDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font_size' => 14,
                'default_font' => 'sarabunTH',
                'margin_left' => 5,
                'margin_right' => 5,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]);
            $mpdf->SetTitle('');
            $mpdf->AddPage();

            if ($Item->type == "IN") {
                $title = 'ใบรับเข้าสินค้า';
            } else {
                $title = 'ใบเบิกออกสินค้า';
            }

            $html = '
            <center><h1>' . $title . '</h1></center>
            <table style="width:100%;">
                <tbody>
                    <tr>
                     
                        <td style="vertical-align: top;">
                           <table style="width:100%;">
                                <tbody>
                                    <tr>
                                        <td align="center" style="border:1px solid #333;">
                                            <h1>ใบสั่งเลขที่</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid #333;">
                                            <h2>' . $Item->code . '</h2>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width:100%; margin-top:15px;">
                                <tbody>
                                    <tr>
                                        <td align="center" style="border:1px solid #333;">
                                            <h1>สถานะของงาน</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid #333;">
                                            <h2>' . $Item->status . '</h2>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width:100%; margin-top:15px;">
                                <tbody>
                                    <tr>
                                        <td align="center" style="border:1px solid #333;">
                                            <h1>วันที่</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid #333;">
                                            <h2>' . $Item->date . '</h2>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                      
                        </td>
                    </tr>
                    ';

            $html .= ' <tr>
                    <td>
                        <table style="width:100%;">
                            <tbody>
                                <tr>
                                    <td style="border:1px solid #333; width:40px;"><p>ลำดับ</p></td>
                                    <td style="border:1px solid #333;"><p>ชื่อสินค้า</p></td>
                                    <td style="border:1px solid #333; width:60px;"><p>จำนวน</p></td>
                                </tr>';
            $n = 0;
            foreach ($Item->lines as $key => $value) {
                $n++;
                $product = Products::find($value['product_id']);
                $html .= '<tr>
                <td style="border:1px solid #333; width:40px;">' . $n . '<p></p></td>
                <td style="border:1px solid #333;"><p>' . $product->name . '</p></td>
                <td style="border:1px solid #333; width:60px;"><p>' . $value['qty'] . '</p></td>
            </tr>';
            }



            $html .= '</tbody>
                            </table>
                        </td>
                    </tr>

                </tbody>
            </table>';

            $mpdf->WriteHTML($html);

            $mpdf->Output();
        }
    }

    public function updateStatus(Request $request)
    {
        $loginBy = $request->login_by;

        $id = $request->id;
        $status = $request->status;

        DB::beginTransaction();

        try {

            $Item = StockTrans::find($id);
            $Item->status = $status;
            $Item->save();

            if ($status == "Finish") {
                $ItemLine = StockTransLine::where('order_id', $Item->id)->get();
                foreach ($ItemLine as $key => $value) {
                    $ItemF = ProductUnit::where('product_id', $value['product_id'])
                        ->where('unit_id', $value['unit_id'])
                        ->first();

                    if ($ItemF) {
                        if ($Item->type == "IN") {
                            $ItemF->qty = $ItemF->qty + $value['qty'];
                        } else {
                            $ItemF->qty = $ItemF->qty - $value['qty'];
                        }
                        $ItemF->save();
                    }
                }
            }

            //

            //log
            $userId = "admin";
            $type = 'เพิ่มรายการ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $request->name;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }
}
