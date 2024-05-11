<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeCredential;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

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
    public function influencerlogin(Request $request){

    }

    public function employeelogin(Request $request)
    {
        // if (!isset($request->email)) {
        //     return $this->returnErrorData('[email] ไม่มีข้อมูล', 404);
        // } else if (!isset($request->password)) {
        //     return $this->returnErrorData('[password] ไม่มีข้อมูล', 404);
        // }

        // $domainname = "@gorilla.com";
        
        // if (strpos($request->email, $domainname) === false) {
        //     return $this->returnErrorData('กรุณาระบุ Email ที่มี domain @gorilla.com', 404);
        // }
        // $user = EmployeeCredential::where('Email', $request->email)
        //     ->where('PasswordHash', md5($request->password))
        //     ->first();

        // if ($user) {

        //     //log
        //     $userId = $request->email;
        //     $type = 'เข้าสู่ระบบ';
        //     $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type;
        //     $this->Log($userId, $description, $type);
        //     //

        //     return response()->json([
        //         'code' => '200',
        //         'status' => true,
        //         'message' => 'เข้าสู่ระบบสำเร็จ',
        //         'data' => $user,
        //         'token' => $this->genToken($user->id, $user),
        //     ], 200);
        // } else {
        //     return $this->returnError('รหัสผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง', 401);
        // }

    }

    public function customerlogin(Request $request){
        
    }

}
