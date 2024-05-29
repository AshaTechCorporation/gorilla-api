<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use Illuminate\Http\Request;
use App\Models\PlatformSocial;
use App\Models\ContentStyle;
use App\Models\Career;
use App\Models\Project;
use App\Models\SubType;
use App\Models\InfluencerCredential;
use App\Models\InfluSocial;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use \Firebase\JWT\JWT;
use App\Http\Controllers\LoginController;
use Mockery\Undefined;

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

    public function getInfluencerTimeline(Request $request)
    {
        $social = $request->platform_social_id;
        $subscribe = $request->subscribe;
        //debug
        $InfluencerProject = Influencer::whereHas('platform_socials', function ($query) use ($social, $subscribe) {
            $query->where('platform_social_id', $social);
            $query->where('subscribe', '>', $subscribe);
        })->get()->toArray();
    
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $InfluencerProject);
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
                $query->select('projects.name as name', 'influencer_project.status', 'influencer_project.project_id');
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
        if ($request->social_name) {
            $D->whereHas('platform_socials', function ($query) use ($request) {
                $query->where('platform_socials.name', $request->social_name);
            });
            if ($request->subtype_id) {
                $subtype = SubType::find($request->subtype_id);
                if ($subtype) {
                    $minSubscribe = $subtype->min;
                    $maxSubscribe = $subtype->max;
                    $D->whereHas('platform_socials', function ($query) use ($minSubscribe, $maxSubscribe, $request) {
                        $query->where('platform_socials.name', $request->social_name)
                            ->whereBetween('subscribe', [$minSubscribe, $maxSubscribe]);
                    });
                }
            }
        }

        $d = $D->paginate($length, ['*'], 'page', $page);
        $d->count = 0;
        if ($d->isNotEmpty()) {

            $No = (($page - 1) * $length);

            foreach ($d as $item) {
                $No++;
                $item->No = $No;
                // Calculate age
                $birthdate = new \DateTime($item->birthday);
                $now = new \DateTime();
                $age = $now->diff($birthdate)->y;

                if ($request->social_name) {
                    $subtypes = Subtype::all();
                    foreach ($subtypes as $subtype) {
                        // $item->count = $item->count + 1;
                        $minSubscribe = $subtype->min;
                        $maxSubscribe = $subtype->max;
                        $socialInflu = $item->platform_socials;
                        foreach ($socialInflu as $social) {
                            if ($social->platform_social_name == $request->social_name) {
                                if ($social->subscribe >= $minSubscribe && $social->subscribe <= $maxSubscribe) {
                                    $item->typefollower = $subtype->name;
                                    // $item->count = "test";
                                    break;
                                }
                            }
                        }
                    }
                    // If no matching subtype found, set typefollower to "-"
                    if (!$item->typefollower) {
                        $item->typefollower = "-";
                    }
                } else {
                    $item->typefollower = "-";
                }

                // Add age to the item
                $item->age = $age;
                // $item->typefollower = "-";
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

    public function Line_Influencer(Request $request)
    {
        try {

            $Login = new LoginController();

            $key = $request->lineid;
            $Item = Influencer::where('line_token', 'like', "{$key}")
                ->first();

            if ($Item) {

                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'data' => $Item->id,
                    'token' => $Login->genToken($Item->id, $Item),
                ], 200);
            } else {


                $latestId = Influencer::latest('id')->value('id');
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'สร้างบัญชีสำเร็จ',
                    'data' => null,
                    'token' => $Login->genToken($latestId, $key),
                ], 200);
            }
        } catch (\Exception $e) {
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }

    public function selfassign(Request $request)
    {

        $loginBy = $this->decodername($request->header('Authorization'));

        $rules = [
            'fullname' => 'required|string|max:255',
            'gender' => 'required|string',
            'phone' => 'required|numeric',
            'career_id' => 'required',
            'content_style_id' => 'required',
            'email' => 'nullable|email|max:255',
            'line_id' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'product_address' => 'nullable|string|max:255',
            'product_province' => 'nullable|string|max:255',
            'product_district' => 'nullable|string|max:255',
            'product_subdistrict' => 'nullable|string|max:255',
            'product_zip' => 'nullable|string|max:10',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account' => 'nullable|string|max:255',
            'bank_brand' => 'nullable|string|max:255',
            'id_card' => 'nullable|string|max:255',
            'name_of_card' => 'nullable|string|max:255',
            'address_of_card' => 'nullable|string|max:255',
            'influencer_province' => 'nullable|string|max:255',
            'influencer_district' => 'nullable|string|max:255',
            'influencer_subdistrict' => 'nullable|string|max:255',
            'influencer_zip' => 'nullable|string|max:10',
            'map' => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
        ];

        $messages = [
            'fullname.required' => 'กรุณาระบุชื่อระดับ',
            'gender.required' => 'กรุณาระบุเพศ',
            'phone.required' => 'กรุณาระบุเบอร์โทร',
            'career_id.exists' => 'ไม่พบ career_id',
            'content_style_id.exists' => 'ไม่พบ content_style_id',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'phone.numeric' => 'เบอร์โทรต้องเป็นตัวเลข',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnErrorData($errors, 422);
        }

        $socialID = $request->socials;

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
            $Item->line_id = $request->line_id;
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
            $Item->current_address = $request->current_address;
            $Item->latitude = $request->latitude;
            $Item->longitude = $request->longitude;
            $Item->note = $request->note;
            // $Item->line_token = $payload->lun;

            if ($request->image_bank && $request->image_bank != null && $request->image_bank != 'null') {
                $Item->image_bank = $this->uploadImage($request->image_bank, '/image_bank');
            }

            if ($request->image_card && $request->image_card != null && $request->image_card != 'null') {
                $Item->image_card = $this->uploadImage($request->image_card, '/image_card');
            }

            $Item->status = "Request";
            $Item->create_by = "admin";

            $Item->save();


            if (isset($request->socials)) {
                $decodedSocials = [];
                try {
                    foreach ($request->socials as $socialJson) {
                        $decodedSocial = json_decode($socialJson, true);
                        if ($decodedSocial !== null) {
                            $decodedSocials[] = $decodedSocial;
                        }
                    }
                    foreach ($decodedSocials as $social) {
                        $socialID = $social["platform_social_id"];
                        $socialObject = PlatformSocial::find($socialID);

                        if ($socialObject == null) {
                            return $this->returnErrorData('ไม่มี platform_social_id นี้ ', 404);
                        } else {
                            $Item->platform_socials()->attach($socialObject, array('name' => $social['name'], 'subscribe' => $social['subscribe'], 'link' => $social['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->' . $e, 404);
                    // return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->'. "before decode :". $request->socials[0] ."after decode :" .$decodedSocials[0], 404);
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

            $Login = new LoginController();
            $tokendata = $request->header('Authorization');
            $credential = $Login->decodeToken($tokendata);
            $influCredential = InfluencerCredential::find($credential->aud);
            $influCredential->influencer_id = $Item->id;
            $influCredential->save();

            DB::commit();
            $Login = new LoginController();
            $latestId = Influencer::latest('id')->value('id');

            return $this->returnSuccess('ดำเนินการสำเร็จ', $latestId);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    public function selfupdate(Request $request)
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

            if ($request->career_id != null) {
                $Item->career_id = $request->career_id;
            } else {
                $Item->career_id = $Item->career_id;
            }

            if ($request->content_style_id != null) {
                $Item->content_style_id = $request->content_style_id;
            } else {
                $Item->content_style_id = $Item->content_style_id;
            }

            if ($request->fullname != null) {
                $Item->fullname = $request->fullname;
            } else {
                $Item->fullname = $Item->fullname;
            }

            if ($request->gender != null) {
                $Item->gender = $request->gender;
            } else {
                $Item->gender = $Item->gender;
            }


            if ($request->email != null) {
                $Item->email = $request->email;
            } else {
                $Item->email = $Item->email;
            }

            if ($request->phone != null) {
                $Item->phone = $request->phone;
            } else {
                $Item->phone = $Item->phone;
            }

            if ($request->line_id != null) {
                $Item->line_id = $request->line_id;
            } else {
                $Item->line_id = $Item->line_id;
            }

            if ($request->birthday != null) {
                $Item->birthday = $request->birthday;
            } else {
                $Item->birthday = $Item->birthday;
            }

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
            $Item->current_address = $request->current_address;
            $Item->latitude = $request->latitude;
            $Item->longitude = $request->longitude;
            $Item->note = $request->note;

            if ($request->image_bank && $request->image_bank != null && $request->image_bank != 'null' && $request->image_bank != 'undefined') {
                $Item->image_bank = $this->uploadImage($request->image_bank, '/image_bank');
            }

            if ($request->image_card && $request->image_card != null && $request->image_card != 'null' && $request->image_card != 'undefined') {
                $Item->image_card = $this->uploadImage($request->image_card, '/image_card');
            }
            $Item->status = "Yes";
            $Item->update_by = $loginBy;

            $Item->save();

            if (isset($request->socials)) {
                $decodedSocials = [];
                try {
                    foreach ($request->socials as $socialJson) {
                        $decodedSocial = json_decode($socialJson, true);
                        if ($decodedSocial !== null) {
                            $decodedSocials[] = $decodedSocial;
                        }
                    }
                    foreach ($decodedSocials as $social) {
                        $socialID = $social["platform_social_id"];
                        $socialObject = PlatformSocial::find($socialID);

                        if ($socialObject == null) {
                            return $this->returnErrorData('ไม่มี platform_social_id นี้ ', 404);
                        } else {
                            $Item->platform_socials()->attach($socialObject, array('name' => $social['name'], 'subscribe' => $social['subscribe'], 'link' => $social['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->' . $e, 404);
                    // return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->'. "before decode :". $request->socials[0] ."after decode :" .$decodedSocials[0], 404);
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

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
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
        $socialID = $request->socials;

        $rules = [
            'fullname' => 'required|string|max:255',
            'gender' => 'required|string',
            'phone' => 'required|numeric',
            'career_id' => 'required',
            'content_style_id' => 'required',
            'email' => 'nullable|email|max:255',
            'line_id' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'product_address' => 'nullable|string|max:255',
            'product_province' => 'nullable|string|max:255',
            'product_district' => 'nullable|string|max:255',
            'product_subdistrict' => 'nullable|string|max:255',
            'product_zip' => 'nullable|string|max:10',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account' => 'nullable|string|max:255',
            'bank_brand' => 'nullable|string|max:255',
            'id_card' => 'nullable|string|max:255',
            'name_of_card' => 'nullable|string|max:255',
            'address_of_card' => 'nullable|string|max:255',
            'influencer_province' => 'nullable|string|max:255',
            'influencer_district' => 'nullable|string|max:255',
            'influencer_subdistrict' => 'nullable|string|max:255',
            'influencer_zip' => 'nullable|string|max:10',
            'map' => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
        ];

        $messages = [
            'fullname.required' => 'กรุณาระบุชื่อระดับ',
            'gender.required' => 'กรุณาระบุเพศ',
            'phone.required' => 'กรุณาระบุเบอร์โทร',
            'career_id.exists' => 'ไม่พบ career_id',
            'content_style_id.exists' => 'ไม่พบ content_style_id',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'phone.numeric' => 'เบอร์โทรต้องเป็นตัวเลข',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnErrorData($errors, 422);
        }

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
            $Item->map = $request->map;
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


            if (isset($request->socials)) {
                $decodedSocials = [];
                try {
                    foreach ($request->socials as $socialJson) {
                        $decodedSocial = json_decode($socialJson, true);
                        if ($decodedSocial !== null) {
                            $decodedSocials[] = $decodedSocial;
                        }
                    }
                    foreach ($decodedSocials as $social) {
                        $socialID = $social["platform_social_id"];
                        $socialObject = PlatformSocial::find($socialID);

                        if ($socialObject == null) {
                            return $this->returnErrorData('ไม่มี platform_social_id นี้ ', 404);
                        } else {
                            $Item->platform_socials()->attach($socialObject, array('name' => $social['name'], 'subscribe' => $social['subscribe'], 'link' => $social['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->' . $e, 404);
                    // return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->'. "before decode :". $request->socials[0] ."after decode :" .$decodedSocials[0], 404);
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

            $latestId = $Item->id;

            $influCredential = new InfluencerCredential();
            $influCredential->influencer_id = $latestId;
            $influCredential->GK = Str::random(5);
            $influCredential->save();

            $Byname = $this->decodername($request->header('Authorization'));
            //log

            $userId = $Byname;
            $type = 'เพิ่มผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);


            DB::commit();
            $Login = new LoginController();
            $latestId = Influencer::latest('id')->value('id');
            $Login->genToken($latestId, $Item);

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
                $query->select('platform_socials.name as platform_social_name', 'influencer_platform_social.name as name', 'subscribe', 'link');
            }])
            ->with(['projects' => function ($query) {
                $query->select('projects.name as name', 'influencer_project.status', 'influencer_project.project_id');
            }])
            ->with('past_project')
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
            $Item->map = $request->map;
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


            // if ($request->socials === "SocialTemp") {
            //     $request->merge([
            //         'socials' => [
            //             [
            //                 'platform_social_id' => 1,
            //                 'name' => 'Facebook',
            //                 'subscribe' => 1000,
            //                 'link' => 'https://www.facebook.com/example'
            //             ],
            //             [
            //                 'platform_social_id' => 2,
            //                 'name' => 'Tiktok',
            //                 'subscribe' => 15000,
            //                 'link' => 'https://www.tiktok.com/example'
            //             ],
            //             [
            //                 'platform_social_id' => 3,
            //                 'name' => 'Youtube',
            //                 'subscribe' => 3000,
            //                 'link' => 'https://www.youtube.com/example'
            //             ]
            //         ]
            //     ]);
            // }


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
    public function destroy(Request $request,$id)
    {
        DB::beginTransaction();

        try {

            $Item = Influencer::find($id);
            $Item->platform_socials()->detach();
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

    public function fixdataInfluencer(Request $request)
    {
        $Byname = $this->decodername($request->header('Authorization'));
        $socialID = $request->socials;
        $id = $request->influencer_id;

        $rules = [
            'fullname' => 'required|string|max:255',
            'gender' => 'required|string',
            'phone' => 'required|numeric',
            'career_id' => 'required',
            'content_style_id' => 'required',
            'email' => 'nullable|email|max:255',
            'line_id' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'product_address' => 'nullable|string|max:255',
            'product_province' => 'nullable|string|max:255',
            'product_district' => 'nullable|string|max:255',
            'product_subdistrict' => 'nullable|string|max:255',
            'product_zip' => 'nullable|string|max:10',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account' => 'nullable|string|max:255',
            'bank_brand' => 'nullable|string|max:255',
            'id_card' => 'nullable|string|max:255',
            'name_of_card' => 'nullable|string|max:255',
            'address_of_card' => 'nullable|string|max:255',
            'influencer_province' => 'nullable|string|max:255',
            'influencer_district' => 'nullable|string|max:255',
            'influencer_subdistrict' => 'nullable|string|max:255',
            'influencer_zip' => 'nullable|string|max:10',
            'map' => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
        ];

        $messages = [
            'fullname.required' => 'กรุณาระบุชื่อระดับ',
            'gender.required' => 'กรุณาระบุเพศ',
            'phone.required' => 'กรุณาระบุเบอร์โทร',
            'career_id.exists' => 'ไม่พบ career_id',
            'content_style_id.exists' => 'ไม่พบ content_style_id',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'phone.numeric' => 'เบอร์โทรต้องเป็นตัวเลข',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnErrorData($errors, 422);
        }

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
            $Item->update_by = $Byname;

            $Item->save();

            if (isset($request->socials)) {
                $decodedSocials = [];
                try {
                    foreach ($request->socials as $socialJson) {
                        $decodedSocial = json_decode($socialJson, true);
                        if ($decodedSocial !== null) {
                            $decodedSocials[] = $decodedSocial;
                        }
                    }
                    foreach ($decodedSocials as $social) {
                        $socialID = $social["platform_social_id"];
                        $socialObject = PlatformSocial::find($socialID);

                        if ($socialObject == null) {
                            return $this->returnErrorData('ไม่มี platform_social_id นี้ ', 404);
                        } else {
                            $Item->platform_socials()->attach($socialObject, array('name' => $social['name'], 'subscribe' => $social['subscribe'], 'link' => $social['link']));
                        }
                    }
                } catch (\Throwable $e) {
                    return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->' . $e, 404);
                    // return $this->returnErrorData('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง ->'. "before decode :". $request->socials[0] ."after decode :" .$decodedSocials[0], 404);
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

            $userId = $Byname;
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
