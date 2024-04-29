<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Influencer;
use App\Models\SubType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class ProjectController extends Controller
{
    public function getList()
    {
        $Item = Project::with('customer')
            ->with('employees')
            ->get()->toarray();

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


        $col = array('id', 'customer_id', 'name', 'strdate', 'enddate', 'numinflu', 'projectdes', 'created_at', 'updated_at');

        $orderby = array('id', 'customer_id', 'name', 'strdate', 'enddate', 'numinflu', 'projectdes', 'created_at');

        $D = Project::select($col)
            ->with('customer')
            ->with('employees')
            ->with(['influencers' => function ($query) {
                $query->with('career')
                    ->with('contentstyle')
                    ->with(['platform_socials' => function ($query) {
                        // Select only the name and subscribe columns from the pivot table
                        $query->select('platform_socials.name as platform_social_name', 'influencer_platform_social.name as name', 'subscribe', 'link');
                    }]);
            }]);
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
            });
        }

        if ($request->work_id) {
            $D->where('projects.id', $request->work_id);
        }

        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);
            foreach ($d as $project) {
                foreach ($project->influencers as $influencer) {
                    $No++;
                    $influencer->No = $No;
                    // Calculate age
                    $birthdate = new \DateTime($influencer->birthday);
                    $now = new \DateTime();
                    $age = $now->diff($birthdate)->y;

                    if ($request->social_name) {
                        $subtypes = Subtype::all();
                        foreach ($subtypes as $subtype) {
                            // $item->count = $item->count + 1;
                            $minSubscribe = $subtype->min;
                            $maxSubscribe = $subtype->max;
                            $socialInflu = $influencer->platform_socials;
                            foreach ($socialInflu as $social) {
                                if ($social->platform_social_name == $request->social_name) {
                                    if ($social->subscribe >= $minSubscribe && $social->subscribe <= $maxSubscribe) {
                                        $influencer->typefollower = $subtype->name;
                                        // $item->count = "test";
                                        break;
                                    }
                                }
                            }
                        }
                        // If no matching subtype found, set typefollower to "-"
                        if (!$influencer->typefollower) {
                            $influencer->typefollower = "-";
                        }
                    } else {
                        $influencer->typefollower = "-";
                    }

                    // Add age to the item
                    $influencer->age = $age;
                    // $item->typefollower = "-";
                    $influencer->image_bank = url($influencer->image_bank);
                    $influencer->image_card = url($influencer->image_card);
                }
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
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
        $employeeID = $request->employees;

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุ name ให้เรียบร้อย', 404);
        } else if (!isset($request->strdate)) {
            return $this->returnErrorData('กรุณาระบุ strdate ให้เรียบร้อย', 404);
        } else if (!isset($request->enddate)) {
            return $this->returnErrorData('กรุณาระบุ enddate ให้เรียบร้อย', 404);
        } else if (!isset($request->productname)) {
            return $this->returnErrorData('กรุณาระบุ productname ให้เรียบร้อย', 404);
        } else if (!isset($request->enddate)) {
            return $this->returnErrorData('กรุณาระบุ enddate ให้เรียบร้อย', 404);
        } else if (!isset($request->productname)) {
            return $this->returnErrorData('กรุณาระบุ productname ให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = new Project();

            $Item->customer_id = $request->customer_id;

            $Customer = Customer::find($Item->customer_id);
            if (!$Customer) {
                return $this->returnErrorData('ไม่พบ customer_id', 404);
            }
            $Item->name = $request->name;
            $Item->strdate = $request->strdate;
            $Item->enddate = $request->enddate;
            $Item->productname = $request->productname;
            $Item->numinflu = $request->numinflu;
            $Item->projectdes = $request->projectdes;

            $Item->save();

            if (isset($request->employees)) {
                for ($i = 0; $i < count($employeeID); $i++) {

                    $employee = Employee::find($employeeID[$i]['employee_id']);

                    if ($employee == null) {
                        return $this->returnErrorData('เกิดข้อผิดพลาดที่ $employee กรุณาลองใหม่อีกครั้ง ', 404);
                    } else {
                        $Item->employees()->attach($employee);
                    }
                }
            }


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
        $checkId = Project::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }
        $Item = Project::with('customer')
            ->where('id', $id)
            ->first();
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project $Project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $Project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project $Project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loginBy = "admin";
        $employeeID = $request->employees;

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุ name ให้เรียบร้อย', 404);
        } else if (!isset($request->strdate)) {
            return $this->returnErrorData('กรุณาระบุ strdate ให้เรียบร้อย', 404);
        } else if (!isset($request->enddate)) {
            return $this->returnErrorData('กรุณาระบุ enddate ให้เรียบร้อย', 404);
        } else if (!isset($request->productname)) {
            return $this->returnErrorData('กรุณาระบุ productname ให้เรียบร้อย', 404);
        } else if (!isset($request->enddate)) {
            return $this->returnErrorData('กรุณาระบุ enddate ให้เรียบร้อย', 404);
        } else if (!isset($request->productname)) {
            return $this->returnErrorData('กรุณาระบุ productname ให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = Project::find($id);

            $Item->customer_id = $request->customer_id;

            $Customer = Customer::find($Item->customer_id);
            if (!$Customer) {
                return $this->returnErrorData('ไม่พบ customer_id', 404);
            }
            $Item->name = $request->name;
            $Item->strdate = $request->strdate;
            $Item->enddate = $request->enddate;
            $Item->productname = $request->productname;
            $Item->numinflu = $request->numinflu;
            $Item->projectdes = $request->projectdes;

            $Item->save();

            if (isset($request->employees)) {
                for ($i = 0; $i < count($employeeID); $i++) {

                    $employee = Employee::find($employeeID[$i]['employee_id']);

                    if ($employee == null) {
                        return $this->returnErrorData('เกิดข้อผิดพลาดที่ $employee กรุณาลองใหม่อีกครั้ง ', 404);
                    } else {
                        $Item->employees()->attach($employee);
                    }
                }
            }
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
     * @param  \App\Models\Project $Project
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Project::find($id);
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


    public function addInfluencer(Request $request)
    {
        $loginBy = "admin";
        $ProjectID = $request->project_id;
        $influencerID = $request->influencers;

        if (empty($ProjectID)) {
            return $this->returnErrorData('กรุณาระบุ $ProjectID ให้เรียบร้อย' . $request, 404);
        }
        DB::beginTransaction();

        try {

            $Item = Project::find($ProjectID);

            if (isset($request->influencers)) {
                for ($i = 0; $i < count($influencerID); $i++) {

                    $influencer = Influencer::find($influencerID[$i]['influencer_id']);

                    if ($influencer == null) {
                        return $this->returnErrorData('เกิดข้อผิดพลาดที่ $influencer กรุณาลองใหม่อีกครั้ง ', 404);
                    } else {
                        $Item->influencers()->attach($influencer, array('status' => $influencerID[$i]['status']));
                    }
                }
            }

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
