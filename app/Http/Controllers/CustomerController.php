<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerCredential;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class CustomerController extends Controller
{
    public function getList()
    {
        $Item = Customer::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;


        $col = array('id', 'name', 'phone', 'email', 'ccode', 'created_at', 'updated_at');

        $orderby = array('id', 'name', 'phone', 'email', 'ccode', 'created_at');

        $D = Customer::select($col);

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
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }

    public function searchData(Request $request)
    {
        try{
            $key = $request->input('key');
            $Item = Customer::where('name','like',"%{$key}%")
            ->limit(20)
            ->get()->toarray();
    
            if (!empty($Item)) {
    
                for ($i = 0; $i < count($Item); $i++) {
                    $Item[$i]['No'] = $i + 1;
                }
            }
    
            return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
        }catch(\Exception $e){
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }
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
        $loginBy = "admin";

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุ ชื่อ ให้เรียบร้อย', 404);
        }else if (!isset($request->phone)) {
            return $this->returnErrorData('กรุณาระบุ เบอร์โทร ให้เรียบร้อย', 404);
        }else if(!isset($request->email)){
            return $this->returnErrorData('กรุณาระบุ อีเมล ให้เรียบร้อย', 404);
        }else if (!isset($request->ccode)) {
            return $this->returnErrorData('กรุณาระบุ ccode ให้เรียบร้อย', 404);
        }
        else

            DB::beginTransaction();

        try {
            $Item = new Customer();
            $Item->name = $request->name;
            $Item->email = $request->email;
            $Item->phone = $request->phone;
            $Item->ccode= $request->ccode;
            
            $Item->save();
            //
            
            $latestId = $Item->id;

            $customerCredential = new CustomerCredential();
            $customerCredential->customer_id = $latestId;
            $customerCredential->UID = $Item->email;
            $customerCredential->save();

            //log
            $userId = $loginBy;
            $type = 'เพิ่มลูกค้า';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);
            

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
     * @param  \App\Models\InfluSocial $influSocial
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checkId = Customer::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }
        $Item = Customer::where('id', $id)
            ->first();  
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loginBy = "admin";

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุ ชื่อ ให้เรียบร้อย', 404);
        }else if (!isset($request->phone)) {
            return $this->returnErrorData('กรุณาระบุ เบอร์โทร ให้เรียบร้อย', 404);
        }else if(!isset($request->email)){
            return $this->returnErrorData('กรุณาระบุ อีเมล ให้เรียบร้อย', 404);
        }else if (!isset($request->ccode)) {
            return $this->returnErrorData('กรุณาระบุ อาชีพ ให้เรียบร้อย', 404);
        }
        else

            DB::beginTransaction();

        try {
            $Item = Customer::find($id);
            $Item->name = $request->name;
            $Item->email = $request->email;
            $Item->phone = $request->phone;
            $Item->ccode= $request->ccode;
            
            $Item->save();
            //

            //log
            $userId = $loginBy;
            $type = 'แก้ไขลูกค้า';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);
            

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
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Customer::find($id);
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

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    
    }
}
