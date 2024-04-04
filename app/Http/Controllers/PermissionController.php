<?php

namespace App\Http\Controllers;

use App\Models\MenuPermission;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{

    public function getList()
    {
        $Permission = Permission::get()->toarray();

        if (!empty($Permission)) {

            for ($i = 0; $i < count($Permission); $i++) {
                $Permission[$i]['No'] = $i + 1;
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Permission);
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

        $col = array('id', 'name', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'name', 'create_by', 'update_by', 'created_at', 'updated_at');

        $d = Permission::select($col);

        if (isset($status)) {
            $d->where('status', $status);
        }

        if ($orderby[$order[0]['column']]) {
            $d->orderby($orderby[$order[0]['column']], $order[0]['dir']);
        }
        if ($search['value'] != '' && $search['value'] != null) {

            //search datatable
            $d->where(function ($query) use ($search, $col) {
                foreach ($col as &$c) {
                    $query->orWhere($c, 'like', '%' . $search['value'] . '%');
                }
            });
        }

        $d = $d->paginate($length, ['*'], 'page', $page);

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

        // $loginBy = $request->login_by;

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุชื่อสิทธิ์การใช้งานระบบให้เรียบร้อย', 404);
        }

        $name = $request->name;

        $checkName = Permission::where('name', $name)->first();

        if ($checkName) {
            return $this->returnErrorData($name . ' มีข้อมูลในระบบแล้ว', 404);
        } else {

            DB::beginTransaction();

            try {

                $permission = new Permission();
                $permission->name = $name;

                $permission->create_by = "admin";
                $permission->updated_at = Carbon::now()->toDateTimeString();

                $permission->save();

                foreach ($request->menu as $key => $value) {

                    $ItemL = new MenuPermission();
                    $ItemL->permission_id = $permission->id;
                    $ItemL->menu_id = $value['menu_id'];

                    if ($value['select_all'] == true) {
                        $ItemL->view = 1;
                        $ItemL->edit = 1;
                        $ItemL->delete = 1;
                        $ItemL->save = 1;
                    } else {
                        $ItemL->view = $value['view'];
                        $ItemL->edit = $value['edit'];
                        $ItemL->delete = $value['delete'];
                        $ItemL->save = $value['save'];
                    }

                    $ItemL->save();
                }

                //log
                $userId = "admin";
                $type = 'เพิ่มสิทธิ์การใช้งาน';
                $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $name;
                $this->Log($userId, $description, $type);
                //

                DB::commit();

                return $this->returnSuccess('ดำเนินการสำเร็จ', $permission);
            } catch (\Throwable $e) {

                DB::rollback();

                return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ', 404);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Permission = Permission::find($id);

        if($Permission){
            $Permission->menus = MenuPermission::where('permission_id',$id)->get();
            foreach ($Permission->menus as $key => $value) {
                $Permission->menus[$key]->menu_id = intval($value['menu_id']);
                $Permission->menus[$key]->view = intval($value['view']);
                $Permission->menus[$key]->edit = intval($value['edit']);
                $Permission->menus[$key]->save = intval($value['save']);
                $Permission->menus[$key]->delete = intval($value['delete']);
            }
        }
        
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Permission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!isset($id)) {
            return $this->returnErrorData('ไม่พบข้อมูล id', 404);
        }
        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุชื่อสิทธิ์การใช้งานระบบให้เรียบร้อย', 404);
        }

        $name = $request->name;


        DB::beginTransaction();

        try {

            $permission = Permission::find($id);
            $permission->name = $name;

            $permission->create_by = "admin";
            $permission->updated_at = Carbon::now()->toDateTimeString();

            $permission->save();

            MenuPermission::where('permission_id',$id)->delete();

            foreach ($request->menu as $key => $value) {

                $ItemL = new MenuPermission();
                $ItemL->permission_id = $permission->id;
                $ItemL->menu_id = $value['menu_id'];

                if ($value['select_all'] == true) {
                    $ItemL->view = 1;
                    $ItemL->edit = 1;
                    $ItemL->delete = 1;
                    $ItemL->save = 1;
                } else {
                    $ItemL->view = $value['view'];
                    $ItemL->edit = $value['edit'];
                    $ItemL->delete = $value['delete'];
                    $ItemL->save = $value['save'];
                }

                $ItemL->save();
            }

            //log
            $userId = "admin";
            $type = 'เพิ่มสิทธิ์การใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $name;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $permission);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง '.$e, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Permission::find($id);
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
