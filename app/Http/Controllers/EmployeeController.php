<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeCredential;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function getList()
    {
        $Item = Employee::with('department')
        ->with('position')
        ->get()->toarray();

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


        $col = array('id','department_id','position_id', 'ecode', 'prefix', 'fname', 'lname', 'nickname', 'phone');

        $orderby = array('id','department_id','position_id', 'ecode', 'prefix', 'fname', 'lname', 'nickname', 'phone');

        $D = Employee::select($col)
            ->with('department')
            ->with('position');

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
            $Item = Employee::where('fname','like',"%{$key}%")
            ->orWhere('lname', 'like', "%{$key}%")
            ->orWhere('nickname', 'like', "%{$key}%")
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
        $loginBy = "admin";

        if (!isset($request->ecode)) {
            return $this->returnErrorData('กรุณาระบุ ecode ให้เรียบร้อย', 404);
        }else if (!isset($request->prefix)) {
            return $this->returnErrorData('กรุณาระบุ prefix ให้เรียบร้อย', 404);
        }else if(!isset($request->fname)){
            return $this->returnErrorData('กรุณาระบุ fname ให้เรียบร้อย', 404);
        }else if (!isset($request->lname)) {
            return $this->returnErrorData('กรุณาระบุ lname ให้เรียบร้อย', 404);
        }else if (!isset($request->nickname)) {
            return $this->returnErrorData('กรุณาระบุ nickname ให้เรียบร้อย', 404);
        }else if (!isset($request->phone)) {
            return $this->returnErrorData('กรุณาระบุ phone	ให้เรียบร้อย', 404);
        }
        else

            DB::beginTransaction();

        try {
            $Item = new Employee();

            $Item->department_id = $request->department_id;
            $Item->position_id = $request->position_id;
            // $Item->credentials_id = $request->credentials_id;

            //get data
            $Department = Department::find($Item->department_id);
            if (!$Department) {
                return $this->returnErrorData('ไม่พบแผนก', 404);
            }
            $Position = Position::find($Item->position_id);
            if (!$Position) {
                return $this->returnErrorData('ไม่พบตำแหน่ง', 404);
            }
            // $Credentials_id = EmployeeCredential::find($Item->credentials_id);
            // if (!$Credentials_id) {
            //     return $this->returnErrorData('ไม่พบพนักงานในระบบ', 404);
            // }

            $Item->ecode = $request->ecode;
            $Item->prefix = $request->prefix;
            $Item->fname = $request->fname;
            $Item->lname= $request->lname;
            $Item->nickname= $request->nickname;
            $Item->phone= $request->phone;
            
            $Item->save();
            //

            //log
            $userId = $loginBy;
            $type = 'เพิ่มพนักงาน';
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
        $checkId = Employee::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }
        $Item = Employee::with('department')
            ->with('position')
            ->where('id', $id)
            ->first();  
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loginBy = "admin";

        if (!isset($request->ecode)) {
            return $this->returnErrorData('กรุณาระบุ ecode ให้เรียบร้อย', 404);
        }else if (!isset($request->prefix)) {
            return $this->returnErrorData('กรุณาระบุ prefix ให้เรียบร้อย', 404);
        }else if(!isset($request->fname)){
            return $this->returnErrorData('กรุณาระบุ fname ให้เรียบร้อย', 404);
        }else if (!isset($request->lname)) {
            return $this->returnErrorData('กรุณาระบุ lname ให้เรียบร้อย', 404);
        }else if (!isset($request->nickname)) {
            return $this->returnErrorData('กรุณาระบุ nickname ให้เรียบร้อย', 404);
        }else if (!isset($request->phone)) {
            return $this->returnErrorData('กรุณาระบุ phone	ให้เรียบร้อย', 404);
        }
        else

            DB::beginTransaction();

        try {
            $Item = Employee::find($id);

            $Item->position_id = $request->position_id;
            $Item->department_id = $request->department_id;

            //get data
            $Department = Department::find($Item->department_id);
            if (!$Department) {
                return $this->returnErrorData('ไม่พบแผนก', 404);
            }
            $Position = Position::find($Item->position_id);
            if (!$Position) {
                return $this->returnErrorData('ไม่พบตำแหน่ง', 404);
            }

            $Item->ecode = $request->ecode;
            $Item->prefix = $request->prefix;
            $Item->fname = $request->fname;
            $Item->lname= $request->lname;
            $Item->nickname= $request->nickname;
            $Item->phone= $request->phone;
            
            $Item->save();
            //

            //log
            $userId = $loginBy;
            $type = 'แก้ไขพนักงาน';
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
     * @param  \App\Models\Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Employee::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบพนักงาน';
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

    public function addCredential(Request $request)
    {
        $loginBy = "admin";

        if (!isset($request->email)) {
            return $this->returnErrorData('กรุณาระบุ email ให้เรียบร้อย', 404);
        }else if (!isset($request->password)) {
            return $this->returnErrorData('กรุณาระบุ password ให้เรียบร้อย', 404);
        }
        DB::beginTransaction();

        try {

            $Item = new EmployeeCredential();
            $Item->Email = $request->email;
            $Item->PasswordHash = md5($request->password);

            $Item->save();
            //log
            $userId = "admin";
            $type = 'เพิ่มพนักงาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type;
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
