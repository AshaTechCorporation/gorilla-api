<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use Illuminate\Http\Request;
use App\Models\PlatformSocial;
use App\Models\ContentStyle;
use App\Models\Career;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class InfluencerController extends Controller
{
    public function getList()
    {
        $Item = Influencer::with('career')
            ->with('contentstyle')
            ->with('platform_socials')
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

        $type = $request->type;

        $col = array('id', 'fullname', 'gender', 'email', 'phone', 'career_id', 'line_id', 'content_style_id', 'birthday', 'product_address', 'product_province', 'product_district', 'product_subdistrict', 'product_zip', 'bank_id', 'bank_account', 'bank_brand', 'id_card', 'name_of_card', 'address_of_card', 'influencer_province', 'influencer_district', 'influencer_subdistrict', 'influencer_zip', 'image_bank', 'image_card', 'note', 'status', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('id', 'fullname', 'gender', 'email', 'phone', 'career_id', 'line_id', 'content_style_id', 'birthday', 'product_address', 'product_province', 'product_district', 'product_subdistrict', 'product_zip', 'bank_id', 'bank_account', 'bank_brand', 'id_card', 'name_of_card', 'address_of_card', 'influencer_province', 'influencer_district', 'influencer_subdistrict', 'influencer_zip', 'image_bank', 'image_card', 'note', 'status', 'create_by');

        $D = Influencer::select($col)
            ->with('career')
            ->with('contentstyle')
            ->with(['platform_socials' => function ($query) {
                // Select only the name and subscribe columns from the pivot table
                $query->select('platform_socials.name as platform_social_name', 'influencer_platform_social.name as name', 'subscribe', 'link');
            }])
            ->with(['projects' => function ($query) {
                $query->select('projects.name as name', 'status', 'influencer_project.project_id');
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

                //search with
                // $query = $this->withPermission($query, $search);
            });
        }
        if ($request->social_id) {
            $D->whereHas('platform_socials', function ($query) use ($request) {
                $query->where('platform_social_id', $request->social_id);
            });
        }
        // $D->image_bank = url($D->image_bank);
        // $D->image_card = url($D->image_card);
        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);

            foreach ($d as $item) {
                $No++;
                $item->No = $No;

                // Calculate age
                $birthdate = new \DateTime($item->birthday);
                $now = new \DateTime();
                $age = $now->diff($birthdate)->y;

                // Add age to the item
                $item->age = $age;
                $item->typefollower = "Nano";
                $item->image_bank = url($item->image_bank);
                $item->image_card = url($item->image_card);
                               
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }


    public function searchData(Request $request)
    {
        try {
            $key = $request->input('key');
            $Item = Influencer::where('fullname', 'like', "%{$key}%")
                ->limit(20)
                ->get()->toarray();
            dd($key);

            if (!empty($Item)) {

                for ($i = 0; $i < count($Item); $i++) {
                    $Item[$i]['No'] = $i + 1;
                }
            }

            return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
        } catch (\Exception $e) {
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
        $socialID = $request->socials;
        $projectID = $request->project_id;

        if (!isset($request->fullname)) {
            return $this->returnErrorData('กรุณาระบุ ชื่อ ให้เรียบร้อย', 404);
        } else if (!isset($request->gender)) {
            return $this->returnErrorData('กรุณาระบุ เพศ ให้เรียบร้อย', 404);
        } else if (!isset($request->phone)) {
            return $this->returnErrorData('กรุณาระบุ เบอร์โทร ให้เรียบร้อย', 404);
        } else if (!isset($request->email)) {
            return $this->returnErrorData('กรุณาระบุ อีเมล ให้เรียบร้อย', 404);
        } else if (!isset($request->line_id)) {
            return $this->returnErrorData('กรุณาระบุ Line ID ให้เรียบร้อย', 404);
        } else if (!isset($request->birthday)) {
            return $this->returnErrorData('กรุณาระบุ วันเกิด ให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = new Influencer();


            $Item->career_id = $request->career_id;
            $Item->content_style_id = $request->content_style_id;

            //get data
            $Content_style = ContentStyle::find($Item->content_style_id);
            if (!$Content_style) {
                return $this->returnErrorData('ไม่พบ content_style_id', 404);
            }
            $Career = Career::find($Item->career_id);
            if (!$Career) {
                return $this->returnErrorData('ไม่พบ career_id', 404);
            }

            $Item->fullname = $request->fullname;
            $Item->gender = $request->gender;
            $Item->email = $request->email;
            $Item->phone = $request->phone;
            // $Item->career = $request->career;
            $Item->line_id = $request->line_id;
            // $Item->content_style = $request->content_style;
            $Item->birthday = $request->birthday;
            $Item->product_address = $request->product_address;
            $Item->product_province = $request->product_province;
            $Item->product_district = $request->product_district;
            $Item->product_subdistrict = $request->product_subdistrict;
            $Item->product_zip = $request->product_zip;
            $Item->bank_id = $request->bank_id;
            $Item->bank_account = $request->bank_account;
            $Item->bank_brand = $request->bank_brand;
            $Item->id_card = $request->id_card;
            $Item->name_of_card = $request->name_of_card;
            $Item->address_of_card = $request->address_of_card;
            $Item->influencer_province = $request->influencer_province;
            $Item->influencer_district = $request->influencer_district;
            $Item->influencer_subdistrict = $request->influencer_subdistrict;
            $Item->influencer_zip = $request->influencer_zip;
            $Item->current_address = $request->current_address;
            $Item->latitude = $request->latitude;
            $Item->longitude = $request->longitude;
            $Item->note = $request->note;

            if ($request->image_bank && $request->image_bank != null && $request->image_bank != 'null') {
                $Item->image_bank = $this->uploadImage($request->image_bank, '/image_bank');
            }

            if ($request->image_card && $request->image_card != null && $request->image_card != 'null') {
                $Item->image_card = $this->uploadImage($request->image_card, '/image_card');
            }

            $Item->status = "Request";
            $Item->create_by = "admin";

            $Item->save();

            if ($request->socials === "SocialTemp") {
                $request->merge([
                    'socials' => [
                        [
                            'platform_social_id' => 1,
                            'name' => 'Facebook',
                            'subscribe' => 1000,
                            'link' => 'https://www.facebook.com/example'
                        ],
                        [
                            'platform_social_id' => 2,
                            'name' => 'Tiktok',
                            'subscribe' => 15000,
                            'link' => 'https://www.tiktok.com/example'
                        ],
                        [
                            'platform_social_id' => 3,
                            'name' => 'Youtube',
                            'subscribe' => 3000,
                            'link' => 'https://www.youtube.com/example'
                        ]
                    ]
                ]);
            }


            if (isset($request->socials)) {
                try {
                    foreach ($request->socials as $socialID) {
                        $social = PlatformSocial::find($socialID['platform_social_id']);

                        if ($social == null) {
                            return $this->returnErrorData('เกิดข้อผิดพลาดที่ $social กรุณาลองใหม่อีกครั้ง ', 404);
                        } else {
                            $Item->platform_socials()->attach($social, array('name' => $socialID['name'], 'subscribe' => $socialID['subscribe'], 'link' => $socialID['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ' . $e, 404);
                }
            }

            if (isset($request->project_id)) {

                $project = Project::find($request->project_id);

                if ($project == null) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดที่ $projects กรุณาลองใหม่อีกครั้ง ', 404);
                } else {
                    $status = "working";
                    $Item->projects()->attach($project, ['status' => $status]);
                }
            }

            //log
            $userId = $loginBy;
            $type = 'เพิ่มผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);


            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ' . $e, 404);
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
        $checkId = Influencer::find($id);
        if (!$checkId) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }
        $Item = Influencer::with('career')
            ->with('contentstyle')
            ->with(['platform_socials' => function ($query) {
                $query->select('platform_socials.name as platform_social_name', 'influencer_platform_social.name as name', 'subscribe');
            }])
            ->with(['projects' => function ($query) {
                $query->select('projects.name as name', 'status', 'influencer_project.project_id');
            }])
            ->where('id', $id)
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
    public function update(Request $request, $id)
    {
        $loginBy = "admin";
        $socialID = $request->socials;

        if (!isset($id)) {
            return $this->returnErrorData('ไม่พบข้อมูล id', 404);
        }

        DB::beginTransaction();
        try {
            $Item = Influencer::find($id);

            $Item->career_id = $request->career_id;
            $Item->content_style_id = $request->content_style_id;
            //get data
            $Content_style = ContentStyle::find($Item->content_style_id);
            if (!$Content_style) {
                return $this->returnErrorData('ไม่พบ content_style_id', 404);
            }
            $Career = Career::find($Item->career_id);
            if (!$Career) {
                return $this->returnErrorData('ไม่พบ career_id', 404);
            }

            $Item->fullname = $request->fullname;
            $Item->gender = $request->gender;
            $Item->email = $request->email;
            $Item->phone = $request->phone;
            // $Item->career_id = $request->career_id;
            $Item->line_id = $request->line_id;
            // $Item->content_style_id = $request->content_style_id;
            $Item->birthday = $request->birthday;
            $Item->product_address = $request->product_address;
            $Item->product_province = $request->product_province;
            $Item->product_district = $request->product_district;
            $Item->product_subdistrict = $request->product_subdistrict;
            $Item->product_zip = $request->product_zip;
            $Item->bank_id = $request->bank_id;
            $Item->bank_account = $request->bank_account;
            $Item->bank_brand = $request->bank_brand;
            $Item->id_card = $request->id_card;
            $Item->name_of_card = $request->name_of_card;
            $Item->address_of_card = $request->address_of_card;
            $Item->influencer_province = $request->influencer_province;
            $Item->influencer_district = $request->influencer_district;
            $Item->influencer_subdistrict = $request->influencer_subdistrict;
            $Item->influencer_zip = $request->influencer_zip;
            $Item->current_address = $request->current_address;
            $Item->latitude = $request->latitude;
            $Item->longitude = $request->longitude;
            $Item->note = $request->note;

            if ($request->image_bank && $request->image_bank != null && $request->image_bank != 'null') {
                $Item->image_bank = $this->uploadImage($request->image_bank, '/image_bank');
            }

            if ($request->image_card && $request->image_card != null && $request->image_card != 'null') {
                $Item->image_card = $this->uploadImage($request->image_card, '/image_card');
            }
            $Item->status = "Yes";
            $Item->update_by = $loginBy;

            $Item->save();


            if ($request->socials === "SocialTemp") {
                $request->merge([
                    'socials' => [
                        [
                            'platform_social_id' => 1,
                            'name' => 'Facebook',
                            'subscribe' => 1000,
                            'link' => 'https://www.facebook.com/example'
                        ],
                        [
                            'platform_social_id' => 2,
                            'name' => 'Tiktok',
                            'subscribe' => 15000,
                            'link' => 'https://www.tiktok.com/example'
                        ],
                        [
                            'platform_social_id' => 3,
                            'name' => 'Youtube',
                            'subscribe' => 3000,
                            'link' => 'https://www.youtube.com/example'
                        ]
                    ]
                ]);
            }


            if (isset($request->socials)) {
                try {
                    foreach ($request->socials as $socialID) {
                        $social = PlatformSocial::find($socialID['platform_social_id']);

                        if ($social == null) {
                            return $this->returnErrorData('เกิดข้อผิดพลาดที่ $social กรุณาลองใหม่อีกครั้ง ', 404);
                        } else {
                            $Item->platform_socials()->attach($social, array('name' => $socialID['name'], 'subscribe' => $socialID['subscribe'], 'link' => $socialID['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ' . $e, 404);
                }
            }


            if (isset($request->project_id)) {

                $project = Project::find($request->project_id);

                if ($project == null) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดที่ $projects กรุณาลองใหม่อีกครั้ง ', 404);
                } else {
                    $status = "working";
                    $Item->projects()->attach($project, ['status' => $status]);
                }
            }
            //log
            $userId = $loginBy;
            $type = 'แก้ไขผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 404);
        }
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

    public function fixdataInfluencer(Request $request)
    {
        $loginBy = "admin";
        $socialID = $request->socials;
        $id = $request->influencer_id;

        if (!isset($id)) {
            return $this->returnErrorData('ไม่พบข้อมูล id', 404);
        }

        DB::beginTransaction();
        try {
            $Item = Influencer::find($id);

            $Item->career_id = $request->career_id;
            $Item->content_style_id = $request->content_style_id;
            //get data
            $Content_style = ContentStyle::find($Item->content_style_id);
            if (!$Content_style) {
                return $this->returnErrorData($request->fullname, 404);
            }
            $Career = Career::find($Item->career_id);
            if (!$Career) {
                return $this->returnErrorData('ไม่พบ career_id', 404);
            }

            $Item->fullname = $request->fullname;
            $Item->gender = $request->gender;
            $Item->email = $request->email;
            $Item->phone = $request->phone;
            // $Item->career_id = $request->career_id;
            $Item->line_id = $request->line_id;
            // $Item->content_style_id = $request->content_style_id;
            $Item->birthday = $request->birthday;
            $Item->product_address = $request->product_address;
            $Item->product_province = $request->product_province;
            $Item->product_district = $request->product_district;
            $Item->product_subdistrict = $request->product_subdistrict;
            $Item->product_zip = $request->product_zip;
            $Item->bank_id = $request->bank_id;
            $Item->bank_account = $request->bank_account;
            $Item->bank_brand = $request->bank_brand;
            $Item->id_card = $request->id_card;
            $Item->name_of_card = $request->name_of_card;
            $Item->address_of_card = $request->address_of_card;
            $Item->influencer_province = $request->influencer_province;
            $Item->influencer_district = $request->influencer_district;
            $Item->influencer_subdistrict = $request->influencer_subdistrict;
            $Item->influencer_zip = $request->influencer_zip;
            $Item->map = $request->map;
            $Item->note = $request->note;

            if ($request->image_bank && $request->image_bank != null && $request->image_bank != 'null') {
                $Item->image_bank = $this->uploadImage($request->image_bank, '/image_bank');
            }

            if ($request->image_card && $request->image_card != null && $request->image_card != 'null') {
                $Item->image_card = $this->uploadImage($request->image_card, '/image_card');
            }
            $Item->status = "Yes";
            $Item->update_by = $loginBy;

            $Item->save();


            if ($request->socials === "SocialTemp") {
                $request->merge([
                    'socials' => [
                        [
                            'platform_social_id' => 1,
                            'name' => 'Facebook',
                            'subscribe' => 1000,
                            'link' => 'https://www.facebook.com/example'
                        ],
                        [
                            'platform_social_id' => 2,
                            'name' => 'Tiktok',
                            'subscribe' => 15000,
                            'link' => 'https://www.tiktok.com/example'
                        ],
                        [
                            'platform_social_id' => 3,
                            'name' => 'Youtube',
                            'subscribe' => 3000,
                            'link' => 'https://www.youtube.com/example'
                        ]
                    ]
                ]);
            }


            if (isset($request->socials)) {
                try {
                    foreach ($request->socials as $socialID) {
                        $social = PlatformSocial::find($socialID['platform_social_id']);

                        if ($social == null) {
                            return $this->returnErrorData('เกิดข้อผิดพลาดที่ $social กรุณาลองใหม่อีกครั้ง ', 404);
                        } else {
                            $Item->platform_socials()->attach($social, array('name' => $socialID['name'], 'subscribe' => $socialID['subscribe'], 'link' => $socialID['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ' . $e, 404);
                }
            }


            if (isset($request->project_id)) {

                $project = Project::find($request->project_id);

                if ($project == null) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดที่ $projects กรุณาลองใหม่อีกครั้ง ', 404);
                } else {
                    $status = "working";
                    $Item->projects()->attach($project, ['status' => $status]);
                }
            }
            //log
            $userId = $loginBy;
            $type = 'แก้ไขผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 404);
        }
    }
}
