<?php

namespace App\Http\Controllers;

use App\Models\ContentStyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;;

class ContentStyleController extends Controller
{
    public function getList()
    {
        $Item = ContentStyle::get()->toarray();

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

        $col = array('id', 'name', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'name', 'create_by');

        $D = ContentStyle::select($col);

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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ], [
            'name.required' => 'กรุณาระบุชื่อคอนเทนท์',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnErrorData($errors, 422);
        }

            DB::beginTransaction();

        try {
            $Item = new ContentStyle();
            $Item->name = $request->name;

            $Item->save();
            //

            $Byname = $this->decodername($request->header('Authorization'));
            //log
            $userId = $Byname;
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
     * @param  \App\Models\ContentStyle  $ContentStyle
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checkId = ContentStyle::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }
        $Item = ContentStyle::where('id', $id)
            ->first();  
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ContentStyle  $ContentStyle
     * @return \Illuminate\Http\Response
     */
    public function edit(ContentStyle $ContentStyle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContentStyle  $ContentStyle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loginBy = $request->login_by;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ], [
            'name.required' => 'กรุณาระบุชื่อคอนเทนท์',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnErrorData($errors, 422);
        }

            DB::beginTransaction();

        try {
            $Item = ContentStyle::find($id);
            $Item->name = $request->name;

            $Item->save();
            //

            $Byname = $this->decodername($request->header('Authorization'));
            //log
            $userId = $Byname;
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContentStyle  $ContentStyle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        DB::beginTransaction();

        try {

            $Item = ContentStyle::find($id);
            $Item->delete();

            $Byname = $this->decodername($request->header('Authorization'));
            //log
            $userId = $Byname;
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
