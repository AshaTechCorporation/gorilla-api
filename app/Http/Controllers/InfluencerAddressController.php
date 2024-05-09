<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfluencerAddressController extends Controller
{
    public $data;
    public function __construct()
        {
            $path = public_path('thailand_address.json');
            $this->data = json_decode(file_get_contents($path), false);
        }
    public function getProvinces()
        {
            try{
                $data = $this->data;
                $provinces = array_map(function ($item) {
                    return $item->province;
                }, $data);
                $provinces = array_unique($provinces);
                $provinces = array_values($provinces);
    
                // $userId = "admin";
                // $type = 'เรียกข้อมูล';
                // $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
                // $this->Log($userId, $description, $type);
                
                return $this->returnSuccess('ดำเนินการสำเร็จ', $provinces);
            }catch (\Throwable $e) {

                return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
            }

        }
        public function getAmphoes(Request $request)
        {
            try{
                $data = $this->data;

                $province = $request->get('province');
        
                if (empty($province) ) {
                    return $this->returnErrorData('โปรดระบุจังหวัด', 400);
                }
                $amphoes = array_filter($data, function ($item) use ($request) {
                    return $item->province == $request->get('province');
                });
                $amphoes = array_map(function ($item) {
                    return $item->amphoe;
                }, $amphoes);
                $amphoes = array_unique($amphoes);        
                $amphoes = array_values($amphoes);

                // $userId = "admin";
                // $type = 'เรียกข้อมูล';
                // $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
                // $this->Log($userId, $description, $type);

                return $this->returnSuccess('ดำเนินการสำเร็จ', $amphoes);
            }catch (\Throwable $e) {

                return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
            }
        }
        public function getTambons(Request $request)
        {
            try{
                $data = $this->data;

                $province = $request->get('province');
                $amphoe = $request->get('amphoe');
        
                if (empty($province) || empty($amphoe)) {
                    return $this->returnErrorData('โปรดระบุจังหวัดและอำเภอ', 400);
                }
                $districts = array_filter($data, function ($item) use ($request) {
                    return $item->amphoe == $request->get('amphoe') && $item->province == $request->get('province');
                });
                $districts = array_map(function ($item) {
                    return $item->district;
                }, $districts);
                $districts = array_unique($districts);  
                $districts = array_values($districts);
                return $this->returnSuccess('ดำเนินการสำเร็จ', $districts);
            }catch (\Throwable $e) {

                return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
            }
        }
        public function getZipcodes(Request $request)
        {
            try{
                $data = $this->data;

                $province = $request->get('province');
                $amphoe = $request->get('amphoe');
                $tambon = $request->get('tambon');
        
                if (empty($province) || empty($amphoe) || empty($tambon)) {
                    return $this->returnErrorData('โปรดระบุจังหวัดและอำเภอและตำบล', 400);
                }

                $zipcodes = array_filter($data, function ($item) use ($request) {
                    return $item->district == $request->get('tambon') && $item->amphoe == $request->get('amphoe') && $item->province == $request->get('province');
                });
                $zipcodes = array_map(function ($item) {
                    return $item->zipcode;
                }, $zipcodes);
                $zipcodes = array_values($zipcodes);
                return $this->returnSuccess('ดำเนินการสำเร็จ', $zipcodes);
            }catch (\Throwable $e) {

                return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
            }
        }
}
