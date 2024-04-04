<?php

namespace App\Http\Controllers;

use App\Models\unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UnitController extends Controller
{

    public function get()
    {
        $Unit = unit::get()->toarray();

        if (!empty($Unit)) {

            for ($i = 0; $i < count($Unit); $i++) {
                $Unit[$i]['No'] = $i + 1;
            }
        }

        return $this->returnSuccess('สำเร็จ',$Unit);
    }

    public function Page(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;

        $type = $request->type;
        $category = $request->category;


        $col = array('id', 'name', 'code', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'name', 'code', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = unit::select($col);

        if ($orderby[$order[0]['column']]) {
            $D->orderby($orderby[$order[0]['column']], $order[0]['dir']);
        }

        if ($search['value'] != '' && $search['value'] != "null") {

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

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุชื่อหน่วยนับให้เรียบร้อย', 404);
        } else if (!isset($request->code)) {
            return $this->returnErrorData('กรุณาระบุรหัสหน่วยนับให้เรียบร้อย', 404);
        }

        $code = $request->code;
        $name = $request->name;

        $checkName = Unit::where(function ($query) use ($code, $name) {
            $query->orWhere('code', $code);
            $query->orWhere('name', $name);
        })
            ->first();

        if ($checkName) {
            return $this->returnErrorData('มีข้อมูลในระบบแล้ว', 404);
        }

        DB::beginTransaction();

        try {

            $Unit = new Unit();
            $Unit->code = $code;
            $Unit->name = $name;
            $Unit->updated_at = Carbon::now()->toDateTimeString();

            $Unit->save();

            //log
            $userId = "admin";
            $type = 'เพิ่มหน่วยนับ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $name;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('สำเร็จ',$Unit);
        } catch (\Throwable $e) {

            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง', 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Unit = Unit::find($id);
        return $this->returnSuccess('สำเร็จ',$Unit);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (!isset($id)) {
            return $this->returnErrorData('ไม่พบข้อมูล id', 404);
        }

        $code = $request->code;
        $name = $request->name;

        $checkName = Unit::where(function ($query) use ($code, $name) {
            $query->orWhere('code', $code);
            $query->orWhere('name', $name);
        })
            ->where('id', '!=', $id)
            ->first();

        if ($checkName) {
            return $this->returnErrorData('มีข้อมูลในระบบแล้ว', 404);
        }

        DB::beginTransaction();

        try {

            $Unit = Unit::find($id);
            $Unit->code = $code;
            $Unit->name = $name;
            $Unit->updated_at = Carbon::now()->toDateTimeString();

            $Unit->save();

            //log
            $userId = "admin";
            $type = 'แก้ไขหน่วยนับ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $Unit->name;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('สำเร็จ',$Unit);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Unit = Unit::find($id);

            $Unit->code = $Unit->code;
            $Unit->save();

            //log
            $userId = "admin";
            $type = 'ลบหน่วยนับ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $Unit->name;
            $this->Log($userId, $description, $type);
            //

            $Unit->delete();

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ',$Unit);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง', 404);
        }
    }
}
