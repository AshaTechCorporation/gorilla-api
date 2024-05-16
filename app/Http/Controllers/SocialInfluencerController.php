<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialInfluencerController extends Controller
{
    public function getTiktokInfo(Request $request)
    {
        $name = $request->url;
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

    public function getYoutubeInfo(Request $request)
    {

        $url = $request->url;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://youtube138.p.rapidapi.com/channel/details/?id={$url}&hl=en&gl=US",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: youtube138.p.rapidapi.com",
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
                $decodedResponse['name'] = $decodedResponse['title'];
                $decodedResponse['subscribe'] = $decodedResponse['stats']['subscribers'];
                $decodedResponse['link'] = "https://www.youtube.com/{$decodedResponse['username']}";

                return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $decodedResponse);
            }
        }
    }

    public function getInstagramInfo(Request $request)
    {
        $name = $request->url;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://instagram-bulk-scraper-latest.p.rapidapi.com/webget_user_id/{$name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: instagram-bulk-scraper-latest.p.rapidapi.com",
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
                $decodedResponse['name'] = $decodedResponse['data']['username'];
                $decodedResponse['subscribe'] = $decodedResponse['data']['followers'];
                $decodedResponse['link'] = "https://www.Instagram.com/{$name}";

                return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $decodedResponse);
            }
        }
    }

    public function getFacebookInfo(Request $request)
    {
        $name = $request->url;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://instagram-bulk-scraper-latest.p.rapidapi.com/webget_user_id/{$name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: instagram-bulk-scraper-latest.p.rapidapi.com",
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
                $decodedResponse['name'] = $decodedResponse['data']['username'];
                $decodedResponse['subscribe'] = $decodedResponse['data']['followers'];
                $decodedResponse['link'] = "https://www.Instagram.com/{$name}";

                return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $decodedResponse);
            }
        }
    }
}
