<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TiktokController extends Controller
{
    public function getUserInfo($name)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://scraptik.p.rapidapi.com/web/get-user?username={$name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: scraptik.p.rapidapi.com",
                "X-RapidAPI-Key: 754782eebemsh20984d0f68e3202p1eb2d7jsn1bed2d568fa0"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        } else {
            // Decode the JSON response
            $decodedResponse = json_decode($response, true);

            // Check if decoding was successful
            if ($decodedResponse === null) {

                return $this->returnErrorData('ไม่สามารถแปลงข้อมูลเป็นรูปแบบ JSON ได้', 500);
            } else {
                
                // format data
                $decodedResponse['name'] = $decodedResponse['userInfo']['user']['nickname'];
                $decodedResponse['subscribe'] = $decodedResponse['userInfo']['stats']['followerCount'];
                $decodedResponse['link'] = "https://www.tiktok.com/@{$name}";

                return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $decodedResponse);
            }
        }
    }
}
