<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InfluencerController extends Controller
{
    public function getList()
    {
        $Item = Influencer::get()->toarray();

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

        $col = array('id', 'fullname', 'gender', 'email', 'phone', 'occupation', 'line_id', 'content_style', 'birthday', 'address', 'province_id', 'bank_brand', 'bank_account', 'bank_number', 'id_card', 'name_of_card', 'address_of_card', 'image_profile', 'image_bank', 'image_card', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'fullname', 'gender', 'email', 'phone', 'occupation', 'line_id', 'content_style', 'birthday', 'address', 'province_id', 'bank_brand', 'bank_account', 'bank_number', 'id_card', 'name_of_card', 'address_of_card', 'image_profile', 'image_bank', 'image_card',  'create_by');

        $D = Influencer::select($col);

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

        if (!isset($request->fullname)) {
            return $this->returnErrorData('กรุณาระบุชื่อ fname ให้เรียบร้อย', 404);
        } else if (!isset($request->phone)) {
            return $this->returnErrorData('กรุณาระบุชื่อ lname ให้เรียบร้อย', 404);
        } else if (!isset($request->email)) {
            return $this->returnErrorData('กรุณาระบุอีเมล์ให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = new Influencer();
            $Item->fullname = $request->fullname;
            $Item->gender = $request->gender;
            $Item->email = $request->email;
            $Item->phone = $request->phone;
            $Item->occupation = $request->occupation;
            $Item->line_id = $request->line_id;
            $Item->content_style = $request->content_style;
            $Item->birthday = $request->birthday;
            $Item->address = $request->address;
            $Item->province_id = $request->province_id;
            $Item->bank_brand = $request->bank_brand;
            $Item->bank_account = $request->bank_account;
            $Item->bank_number = $request->bank_number;
            $Item->id_card = $request->id_card;
            $Item->name_of_card = $request->name_of_card;
            $Item->address_of_card = $request->address_of_card;

            if ($request->image_bank && $request->image_bank != null && $request->image_bank != 'null') {
                $Item->image_bank = $this->uploadImage($request->image_bank, '/images/banks/');
            }

            if ($request->image_card && $request->image_card != null && $request->image_card != 'null') {
                $Item->image_card = $this->uploadImage($request->image_card, '/images/cards/');
            }

            $Item->status = "Request";
            $Item->create_by = "admin";

            $Item->save();
            //

            //log
            $userId = "admin";
            $type = 'เพิ่มผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
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
     * @param  \App\Models\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Influencer::where('id', $id)
            ->first();
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function edit(Influencer $influencer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Influencer $influencer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Influencer::find($id);
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
