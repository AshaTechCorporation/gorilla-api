<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InfluencerCredential;
use App\Models\CustomerCredential;
use App\Models\Employee;
use App\Models\EmployeeCredential;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OtpController;

class LoginController extends Controller
{
    public $key = "key";

    public function genToken($id, $name)
    {
        $payload = array(
            "iss" => "key",
            "aud" => $id,
            "lun" => $name,
            "iat" => Carbon::now()->timestamp,
            // "exp" => Carbon::now()->timestamp + 86400,
            "exp" => Carbon::now()->timestamp + 31556926,
            "nbf" => Carbon::now()->timestamp,
        );

        $token = JWT::encode($payload, $this->key);
        return $token;
    }

    public function decodeToken($token)
    {
        $token = str_replace('Bearer ', '', $token);
        $payload = JWT::decode($token, $this->key, array('HS256'));
        return $payload;
    }

    public function checkLogin(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);

        try {

            if ($token == "") {
                return $this->returnError('Token Not Found', 401);
            }

            $payload = JWT::decode($token, $this->key, array('HS256'));
            $payload->exp = Carbon::now()->timestamp + 86400;
            $token = JWT::encode($payload, $this->key);

            return response()->json([
                'code' => '200',
                'status' => true,
                'message' => 'Active',
                'data' => [],
                'token' => $token,
            ], 200);
        } catch (\Firebase\JWT\ExpiredException $e) {

            list($header, $payload, $signature) = explode(".", $token);
            $payload = json_decode(base64_decode($payload));
            $payload->exp = Carbon::now()->timestamp + 86400;
            $token = JWT::encode($payload, $this->key);

            return response()->json([
                'code' => '200',
                'status' => true,
                'message' => 'Token is expire',
                'data' => [],
                'token' => $token,
            ], 200);
        } catch (Exception $e) {
            return $this->returnError('Can not verify identity', 401);
        }
    }

    // public function login(Request $request)
    // {
    //     if (!isset($request->username)) {
    //         return $this->returnErrorData('[username] ไม่มีข้อมูล', 404);
    //     } else if (!isset($request->password)) {
    //         return $this->returnErrorData('[password] ไม่มีข้อมูล', 404);
    //     }

    //     $user = User::where('username', $request->username)
    //         ->where('password', md5($request->password))
    //         ->first();

    //     if ($user) {

    //         $user->menus = MenuPermission::where('permission_id',$user->permission_id)->get();
    //         foreach ($user->menus as $key => $value) {
    //             $user->menus[$key]->menu_id = intval($user->menus[$key]->menu_id);
    //             $user->menus[$key]->view = intval($user->menus[$key]->view);
    //             $user->menus[$key]->edit = intval($user->menus[$key]->edit);
    //             $user->menus[$key]->save = intval($user->menus[$key]->save);
    //             $user->menus[$key]->delete = intval($user->menus[$key]->delete);
    //         }

    //         //log
    //         $username = $user->username;
    //         $log_type = 'เข้าสู่ระบบ';
    //         $log_description = 'ผู้ใช้งาน ' . $username . ' ได้ทำการ ' . $log_type;
    //         $this->Log($username, $log_description, $log_type);
    //         //

    //         return response()->json([
    //             'code' => '200',
    //             'status' => true,
    //             'message' => 'เข้าสู่ระบบสำเร็จ',
    //             'data' => $user,
    //             'token' => $this->genToken($user->id, $user),
    //         ], 200);
    //     } else {
    //         return $this->returnError('รหัสผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง', 401);
    //     }

    // }
    public function influencerlogin(Request $request)
    {

        try {

            $Login = new LoginController();

            $key = $request->lineid;

            if ($request->gk) {
                $Item = InfluencerCredential::where('GK', $request->gk)
                    ->first();
                DB::beginTransaction();

                $Item->UID = $key;
                $Item->save();

                DB::commit();
            } else {
                $Item = InfluencerCredential::where('UID', $key)
                    ->first();
            }
            if ($Item) {
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'id' => $Item->influencer_id,
                    'role' => 'Influencer',
                    'token' => $Login->genToken($Item->id, $key),
                ], 200);
            } else {

                DB::beginTransaction();

                $influCredential = new InfluencerCredential();
                $influCredential->UID = $key;
                $influCredential->save();

                DB::commit();
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'สร้างบัญชีสำเร็จ',
                    'id' => null,
                    'role' => 'Influencer',
                    'token' => $Login->genToken($influCredential->id, $key),
                ], 200);
            }
        } catch (\Exception $e) {

            DB::rollback();
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }

    public function influencerOTPlogin(Request $request)
    {

        try {

            $Login = new LoginController();

            $key = $request->phone;

            if ($request->gk) {
                $Item = InfluencerCredential::where('GK', $request->gk)
                    ->first();
                DB::beginTransaction();

                $Item->UID = $key;
                $Item->save();

                DB::commit();
            } else {
                $Item = InfluencerCredential::where('UID', $key)
                    ->first();
            }
            if ($Item) {
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'id' => $Item->influencer_id,
                    'role' => 'Influencer',
                    'token' => $Login->genToken($Item->id, $key),
                ], 200);
            } else {

                DB::beginTransaction();

                $influCredential = new InfluencerCredential();
                $influCredential->UID = $key;
                $influCredential->save();

                DB::commit();

                $otp = new OtpController();
                $otptoken = $otp->sendOTP($key, true);


                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'สร้างบัญชีสำเร็จ',
                    'id' => $influCredential->id,
                    'role' => 'Influencer',
                    'token' => $Login->genToken($influCredential->id, $key),
                    'otptoken' => $otptoken
                ], 200);
            }
        } catch (\Exception $e) {

            DB::rollback();
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }

    public function InfluencerCreateUser(Request $request)
    {
        $data = $request->data;
        $id = $data['id'];
        try {
            if (!isset($data['username'])) {
                return $this->returnErrorData('[username] ไม่มีข้อมูล', 404);
            } else if (!isset($data['password'])) {
                return $this->returnErrorData('[password] ไม่มีข้อมูล', 404);
            }
            $Login = new LoginController();

            $Item = InfluencerCredential::find($id);

            $Item->username = $data['username'];
            $Item->password = md5($data['password']);
            $Item->save();

            if ($Item) {
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'สร้าง User สำเร็จ',
                    'id' => $Item->influencer_id,
                    'role' => 'Influencer',
                    'token' => $Login->genToken($Item->id, $Item->UID),
                ], 200);
            }
        } catch (\Exception $e) {

            DB::rollback();
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }

    public function InfluencerLoginwithPassword(Request $request)
    {
        $data = $request->data;
        try {

            $Login = new LoginController();

            if (!isset($data['username'])) {
                return $this->returnErrorData('[username] ไม่มีข้อมูล', 404);
            } else if (!isset($data['password'])) {
                return $this->returnErrorData('[password] ไม่มีข้อมูล', 404);
            }

            $Item = InfluencerCredential::where('username', $data['username'])
                ->where('password', md5($data['password']))
                ->first();

            if ($Item) {
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'id' => $Item->influencer_id,
                    'phone' => $Item->UID,
                    'role' => 'Influencer',
                    'token' => $Login->genToken($Item->id, $Item->UID),
                ], 200);
            }else{
                return $this->returnErrorData('ไม่พบ Username หรือ Password', 404);
            }
        } catch (\Exception $e) {

            return $this->returnErrorData($e->getMessage(), 404);
        }
    }

    public function employeelogin(Request $request)
    {
        try {

            $Login = new LoginController();

            $key = $request->email;

            $Item = EmployeeCredential::where('UID', $key)
                ->first();

            if ($Item) {
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'id' => $Item->employee_id,
                    'role' => 'Employee',
                    'data' => $Item,
                    'token' => $Login->genToken($Item->id, $Item->UID),
                ], 200);
            } else {

                return response()->json([
                    'code' => '404',
                    'status' => false,
                    'message' => 'ไม่มีบัญชีนี้ในระบบ',
                    'id' => null,
                    'role' => 'Employee',
                ], 200);
            }
        } catch (\Exception $e) {

            DB::rollback();
            return $this->returnErrorData($e, 404);
        }
    }

    public function customerlogin(Request $request)
    {
        try {

            $Login = new LoginController();


            $key = $request->email;

            $Item = CustomerCredential::where('UID', $key)
                ->first();

            if ($Item) {
                return response()->json([
                    'code' => '200',
                    'status' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'id' => $Item->customer_id,
                    'role' => 'Customer',
                    'token' => $Login->genToken($Item->id, $key),
                ], 200);
            } else {

                return response()->json([
                    'code' => '404',
                    'status' => false,
                    'message' => 'test',
                    'id' => null,
                    'role' => null,
                ], 200);
            }
        } catch (\Exception $e) {

            DB::rollback();
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }
}
