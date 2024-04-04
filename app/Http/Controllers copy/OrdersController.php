<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Factory;
use App\Models\OrderList;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function getList()
    {
        $Item = Orders::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['client'] = Clients::find($Item[$i]['client_id']);
                $Item[$i]['order_lists'] = OrderList::where('order_id',$Item[$i]['id'])->get();
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


        $col = array('id', 'code', 'date', 'client_id', 'total_price', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'code', 'date', 'client_id', 'total_price', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Orders::select($col);


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
                $d[$i]->client = Clients::find($d[$i]->client_id);
                $d[$i]->order_lists = OrderList::where('order_id',$d[$i]->id)->get();

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
            $id = IdGenerator::generate(['table' => 'orders', 'field' => 'code', 'length' => 9, 'prefix' => $prefix]);

            $ItemC = new Clients();
            $ItemC->name = $request->client_name;
            $ItemC->phone = $request->client_phone;
            $ItemC->email = $request->client_email;
            $ItemC->address = $request->client_address;

            $ItemC->save();

            $Item = new Orders();
            $Item->code = $id;
            $Item->date = $request->date;
            $Item->total_price = $request->total_price;

            $Item->client_id = $ItemC->id;
            $Item->save();


            foreach ($request->products as $key => $value) {

                $checkStock = Products::find($value['product_id']);
                if ($checkStock) {
                    if ($checkStock->qty < $value['qty']) {
                        $qtyOrder = $value['qty'] - $checkStock->qty;

                        $prefix = "#FAC-";
                        $id = IdGenerator::generate(['table' => 'factories', 'field' => 'code', 'length' => 13, 'prefix' => $prefix]);

                        $ItemF = new Factory();
                        $ItemF->code = $id;
                        $ItemF->date = date('Y-m-d');
                        $ItemF->order_id = $Item->id;
                        $ItemF->product_id = $value['product_id'];
                        $ItemF->qty = $qtyOrder;
                        $ItemF->detail = "สินค้าไม่เพียงพอต่อการจำหน่าย ขาดไปทั้งหมด ".$qtyOrder." ชิ้น จำเป็นต้องสั่งผลิต";
                        $ItemF->save();
                    }
                }

                $ItemL = new OrderList();
                $ItemL->order_id = $Item->id;
                $ItemL->product_id = $value['product_id'];
                $ItemL->qty = $value['qty'];
                $ItemL->cost = $value['cost'];
                $ItemL->price = $value['price'];
                $ItemL->save();
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

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ', 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Orders::where('id', $id)
            ->first();

        if ($Item) {
            $Item->client = Clients::find($Item->client_id);
            $Item->order_lists = OrderList::where('order_id', $id)->get();
            foreach ($Item->order_lists as $key => $value) {
                $Item->order_lists[$key]->product = Products::find($value->product_id);
            }
            
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Orders::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnUpdate('ดำเนินการสำเร็จ');
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ', 404);
        }
    }


    public function dashboard()
    {
        // DB::beginTransaction();

        try {
            $Item['bests']['best_saller'] = 482; 
            $Item['bests']['best_sale_item_qty'] = 3123; 
            $Item['bests']['best_outstock'] = 4114; 

            $Item['last_weeks']['mon'] = 10; 
            $Item['last_weeks']['tue'] = 50; 
            $Item['last_weeks']['wed'] = 20; 
            $Item['last_weeks']['thu'] = 32; 
            $Item['last_weeks']['fri'] = 56; 
            $Item['last_weeks']['sat'] = 80; 
            $Item['last_weeks']['son'] = 90; 


            $Item['months'][1] = 2084; 
            $Item['months'][2] = 4972; 
            $Item['months'][3] = 1048; 
            $Item['months'][4] = 5027; 
            $Item['months'][5] = 1012; 
            $Item['months'][5] = 5021; 
            $Item['months'][6] = 2120; 
            $Item['months'][7] = 5048; 
            $Item['months'][8] = 2845; 
            $Item['months'][9] = 4937; 
            $Item['months'][10] = 3123; 
            $Item['months'][11] = 4109; 
            $Item['months'][12] = 4841; 


            $Item['graph']['complete'] = 10; 
            $Item['graph']['waiting'] = 30; 
            $Item['graph']['delivery'] = 50; 
            $Item['graph']['unaction'] = 10; 


            $Users = User::get()->toarray();
            for ($i = 0; $i < count($Users); $i++) {
                $Users[$i]['total'] = 259;
            }
            $Item['users'] = $Users;
            

            // DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            // DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }
}
