<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\FactoryProductRaw;
use App\Models\Orders;
use App\Models\ProductRaw;
use App\Models\Products;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Signature;

class FactoryController extends Controller
{
    public function getList()
    {
        $Item = Factory::get()->toarray();

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
        $status = $request->status;


        $col = array('id', 'code', 'date', 'order_id', 'product_id', 'qty', 'detail', 'status', 'approve', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'code', 'date', 'order_id', 'product_id', 'qty', 'detail', 'status', 'approve', 'create_by', 'update_by', 'created_at', 'updated_at');

        if ($status) {
            $D = Factory::where('status', $status)->select($col);
        } else {
            $D = Factory::select($col);
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
                $d[$i]->order = Orders::find($d[$i]->order_id);
                $d[$i]->product = Products::find($d[$i]->product_id);
                // $d[$i]->signature = Signature::find($d[$i]);
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
            $prefix = "#FAC-";
            $id = IdGenerator::generate(['table' => 'factories', 'field' => 'code', 'length' => 13, 'prefix' => $prefix]);

            $Item = new Factory();
            $Item->code = $id;
            $Item->date = $request->date;
            $Item->product_id = $request->product_id;
            $Item->qty = $request->qty;
            $Item->detail = $request->detail;
            $Item->save();

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
     * @param  \App\Models\Factory  $factory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Factory::where('id', $id)
            ->first();

        if ($Item) {
            $Item->order = Orders::find($Item->order_id);
            $Item->product = Products::find($Item->product_id);
            $Item->raws = ProductRaw::where('product_id', $Item->id)->get();
            foreach ($Item->raws as $key => $value) {
                $Item->raws[$key]->product = Products::find($value['raw_id']);

                if($Item->raws[$key]->product->stock_status == 1){
                    $itemFactRaw = FactoryProductRaw::where('product_id',$value['raw_id'])
                    ->where('factorie_id',$id)
                    ->first();

                    if($itemFactRaw){
                        $Item->raws[$key]->product->qty = $itemFactRaw->remark_qty;
                    }
                }
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Factory  $factory
     * @return \Illuminate\Http\Response
     */
    public function edit(Factory $factory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Factory  $factory
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
            $Item = Factory::find($id);
            $Item->date = $request->date;
            $Item->product_id = $request->product_id;
            $Item->qty = $request->qty;
            $Item->detail = $request->detail;

            $Item->save();

            foreach ($request->raws as $key => $value) {
                $ItemL = new FactoryProductRaw();
                $ItemL->factorie_id = $id;
                $ItemL->product_id = $value['product_id'];
                $ItemL->remark_qty = $value['remark_qty'];
                $ItemL->save();
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
     * @param  \App\Models\Factory  $factory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Factory::find($id);
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

    public function updateStatus(Request $request)
    {
        $loginBy = $request->login_by;

        $id = $request->id;
        $status = $request->status;

        DB::beginTransaction();

        try {

            $Item = Factory::find($id);
            $Item->status = $status;
            $Item->save();

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

    public function approveStatus(Request $request)
    {
        $loginBy = $request->login_by;

        $id = $request->id;
        $approve = $request->approve;

        DB::beginTransaction();

        try {

            $Item = Factory::find($id);
            $Item->approve = $approve;
            $Item->remark = $request->remark;
            $Item->save();

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

    public function exportPDF($id)
    {

        $Item = Factory::find($id);

        if ($Item) {
            $Item->order = Orders::find($Item->order_id);
            $Item->product = Products::find($Item->product_id);

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

            $title = 'ใบสั่งผลิต';


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
                                            <h2>' . $Item->order->code . '</h2>
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
            $n++;
            $html .= '<tr>
                <td style="border:1px solid #333; width:40px;">' . $n . '<p></p></td>
                <td style="border:1px solid #333;"><p>' . $Item->product->name . '</p></td>
                <td style="border:1px solid #333; width:60px;"><p>' . $Item->qty . '</p></td>
            </tr>';



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
}
