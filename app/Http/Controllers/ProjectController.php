<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Influencer;
use App\Models\SubType;

use App\Http\Controllers\LineNotifyProjectController;
use App\Models\Product;
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


        $col = array('id', 'customer_id', 'name', 'strdate', 'enddate','pcode', 'status',  'created_at', 'updated_at');

        $orderby = array('id', 'customer_id', 'name', 'strdate', 'enddate','pcode', 'status' , 'created_at');

        $D = Project::select($col)
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
            if ($request->social_name) {
                $D = Project::query();

                $D->with(['influencers' => function ($query) use ($request) {
                    if ($request->social_name) {
                        $query->whereHas('platform_socials', function ($query) use ($request) {
                            $query->where('platform_socials.name', $request->social_name);
                        });
                    }
                    $query->with('career')
                        ->with('contentstyle')
                        ->with(['platform_socials' => function ($query) {
                            // Select only the name and subscribe columns from the pivot table
                            $query->select('platform_socials.name as platform_social_name', 'influencer_platform_social.name', 'subscribe', 'link');
                        }]);
                }])
                    ->where('projects.id', $request->work_id);
                if ($request->subtype_id) {
                    $subtype = SubType::find($request->subtype_id);
                    if ($subtype) {
                        $minSubscribe = $subtype->min;
                        $maxSubscribe = $subtype->max;
                        $D->with(['influencers' => function ($query) use ($request, $minSubscribe, $maxSubscribe) {
                            $query->whereHas('platform_socials', function ($query) use ($request, $minSubscribe, $maxSubscribe) {
                                $query->where('platform_socials.name', $request->social_name)
                                    ->whereBetween('subscribe', [$minSubscribe, $maxSubscribe]);
                            });
                            $query->with('career')
                                ->with('contentstyle')
                                ->with(['platform_socials' => function ($query) {
                                    // Select only the name and subscribe columns from the pivot table
                                    $query->select('platform_socials.name as platform_social_name', 'influencer_platform_social.name', 'subscribe', 'link');
                                }]);
                        }]);
                    }
                }
            }
        }

        if ($request->page_type == 'customer') {
            $customer_id = $this->decoderid($request->header('Authorization'));
            $D->where('customer_id', $customer_id);
        }

        if ($request->status) {
            $D->where('status', $request->status);
        }
        
        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);
            foreach ($d as $project) {
                $project->strdate = date('d/m/Y', strtotime($project->strdate . ' +543 years'));
                $project->enddate = date('d/m/Y', strtotime($project->enddate . ' +543 years'));

                $customer = Customer::find($project->customer_id);
                $project->customer_name = $customer->name;
                foreach ($project->influencers as $influencer) {
                    $influencer->count = 0;
                    $No++;
                    $influencer->No = $No;
                    // Calculate age
                    $birthdate = new \DateTime($influencer->birthday);
                    $now = new \DateTime();
                    $age = $now->diff($birthdate)->y;

                    if ($request->social_name) {
                        $subtypes = Subtype::all();
                        foreach ($subtypes as $subtype) {
                            $minSubscribe = $subtype->min;
                            $maxSubscribe = $subtype->max;
                            $socialInflu = $influencer->platform_socials;
                            foreach ($socialInflu as $social) {
                                $influencer->count = $social;
                                if ($social->platform_social_name == $request->social_name) {
                                    if ($social->subscribe >= $minSubscribe && $social->subscribe <= $maxSubscribe) {
                                        $influencer->typefollower = $subtype->name;
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

    public function UpdateProjectStatus($id)
    {
        $loginBy = "admin";
        try {

            DB::beginTransaction();

            $Line = new LineNotifyProjectController;
            $project = Project::find($id);
            $message_data =
                "แจ้งเตือนโปรเจ็ค : " . $project->name . " 📱 \n" .
                "มีการอัปเดตสถานะโปรเจ็คจาก " . "\n" .  $project->status . " เป็น ";
            if ($project) {
                if ($project->status == "open") {
                    $project->status = "ongoing";
                    $message_data = $message_data . "ongoing";
                    $Line->NoticeLine($message_data);
                } elseif ($project->status == "ongoing") {
                    $project->status = "closed";
                    $message_data = $message_data . "closed";
                    $Line->NoticeLine($message_data);
                } else {
                    $message_data = "โปรเจคนี้ปิดไปแล้วครับพี่ 😅";
                    $Line->NoticeLine($message_data);
                }
                $project->save();

                //log
                $userId = $loginBy;
                $type = 'อัปเดตสถานะโปรเจ็ค';
                $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
                $this->Log($userId, $description, $type);
                DB::commit();
                return $this->returnSuccess('อัปเดตสถานะโปรเจคสําเร็จ ', $project);
            } else {
                return $this->returnErrorData('ไม่มีโปรเจคนี้ในระบบ ', 404);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาดในการอัปเดคสถานะโปรเจค ' . $e, 404);
        }
    }


    public function getProjectbyInfluencer($id)
    {
        $influencer = Influencer::find($id);
        if (!$influencer) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }


        $projects = $influencer->projects()
        ->wherePivot('influencer_id', $id)
        ->wherePivot('status', 'working')
        ->get();


        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $projects);
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

        DB::beginTransaction();

        try {
            $Item = new Project();

            $Item->name = $request->name;
            $Item->strdate = $request->strdate;
            $Item->enddate = $request->enddate;
            $Item->customer_id = $request->customer_id;
            $Item->pcode = md5(rand(0, 999) .$request->name);

            $Item->save();

            if (isset($request->products)) {
                $products = $request->products;
                foreach ($products as $product) {

                    $ItemP = new Product();
                    $ItemP->project_id = $Item->id;
                    $ItemP->name = $product['name'];
                    $ItemP->productdes = $product['productdes'];
                    $ItemP->save();
                    if (isset($product['employees'])) {
                        $employees = $product['employees'];
                        foreach ($employees as $employee) {
                            $employee = Employee::find($employee['employee_id']);

                            if ($employee == null) {
                                return $this->returnErrorData('เกิดข้อผิดพลาดที่ $employee กรุณาลองใหม่อีกครั้ง ', 404);
                            } else {
                                $ItemP->employees()->attach($employee);
                            }
                        }
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


        $Item = Project::with('products.employees')
            ->with('customer')
            ->where('id', $id)
            ->first();

        $Item->strdate = date('d/m/Y', strtotime($Item->strdate));
        $Item->enddate = date('d/m/Y', strtotime($Item->enddate));

        $customer = Customer::find($Item->customer_id);
        $Item->customer_name = $customer->name;
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    public function showbyid($id)
    {
        $checkId = Project::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }


        $Item = Project::with('products.employees')
            ->with('customer')
            ->where('id', $id)
            ->first();

        // $Item->strdate = date('d/m/Y', strtotime($Item->strdate));
        // $Item->enddate = date('d/m/Y', strtotime($Item->enddate));

        $customer = Customer::find($Item->customer_id);
        $Item->customer_name = $customer->name;
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
    
        DB::beginTransaction();
    
        try {
            $Item = Project::find($id);
            if (!$Item) {
                return $this->returnErrorData('Project not found', 404);
            }
    
            $Item->name = $request->name;
            $Item->strdate = $request->strdate;
            $Item->enddate = $request->enddate;
            $Item->customer_id = $request->customer_id;
    
            $Item->save();
    
            if (isset($request->products)) {
                $products = $request->products;
                foreach ($products as $product) {
                    if (isset($product['id'])) {
                        $ItemP = Product::find($product['id']);
                        if (!$ItemP) {
                            return $this->returnErrorData('Product not found', 404);
                        }
                    } else {
                        // Create new product
                        $ItemP = new Product();
                        $ItemP->project_id = $Item->id;
                    }
    
                    $ItemP->name = $product['name'];
                    $ItemP->productdes = $product['productdes'];
                    $ItemP->save();
    
                    if (isset($product['employees'])) {
                        // Sync employees
                        $employeeIds = [];
                        foreach ($product['employees'] as $employee) {
                            $employeeEntity = Employee::find($employee['employee_id']);
                            if ($employeeEntity == null) {
                                return $this->returnErrorData('Employee not found', 404);
                            } else {
                                $employeeIds[] = $employeeEntity->id;
                            }
                        }
                        $ItemP->employees()->sync($employeeIds);
                    }
                }
            }
    
            // Log
            $userId = $loginBy;
            $type = 'แก้ไขลูกค้า';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการแก้ไขลูกค้า';
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
            $Item->influencers()->detach();
            $Item->employees()->detach();


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
        $influencerID = $request->influ;

        if (empty($ProjectID)) {
            return $this->returnErrorData('กรุณาระบุ $ProjectID ให้เรียบร้อย' . $request, 404);
        }
        DB::beginTransaction();

        try {

            $Item = Project::find($ProjectID);

            if ($influencerID) {
                foreach($influencerID as $Influencerid) {

                    $influencer = Influencer::find($Influencerid);

                    if ($influencer == null) {
                        return $this->returnErrorData('เกิดข้อผิดพลาดที่ $influencer กรุณาลองใหม่อีกครั้ง ', 404);
                    } else {
                        $Item->influencers()->attach($influencer, array('status' => "working"));
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

    public function getallProductInProject($id)
    {
        // Check if the project with the given ID exists
        $project = Project::find($id);
        if (!$project) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }

        // Fetch the project along with its associated products
        $projectWithProducts = Project::with(['products' => function ($query) use ($id) {
            $query->where('project_id', $id);
        }])->where('id', $id)->first();

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $projectWithProducts);
    }

    public function getProjectbyPcode(Request $request)
    {

        $pcode = $request->pcode;
        DB::beginTransaction();

        try {

            $Item = Project::where("pcode",$pcode)->first();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {


            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }
}
