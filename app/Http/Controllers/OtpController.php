<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class OtpController extends Controller
{
    public function sendOTP($tel,$open)
    {

        try {
            // เชค open otp
            if ($open == true) {

                $body = [
                    'key' => "1795120423986985",
                    'secret' => "a10fb7d5cbe4cce5682faafa6846948a",
                    'msisdn' => $tel
                ];
                // $body = [
                //     'key' => "1792559682800262",
                //     'secret' => "7eb74daa0d60779672dfd6684ec621d4",
                //     'msisdn' => $tel
                // ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                ])->post('https://otp.thaibulksms.com/v2/otp/request', $body);

                if ($response->status() === 200) {
                    $res = $response->body();
                    $data = json_decode($res);
                    $token = $data;
                    
                    return $token;
                } elseif ($response->status() === 400) {
                    $data['status'] = 'failed';
                    $data['token'] = null;
                    $data['refno'] =  null;

                    return $data;
                } else {
                    $data['status'] = 'failed';
                    $data['token'] = null;
                    $data['refno'] =  null;

                    return $data;
                }
            } else {

                // random otp
                $otpKey = $this->randomOtp();

                $data['status'] = 'success';
                $data['token'] = $otpKey['otp_ref'];
                $data['refno'] = $otpKey['otp_ref'];

                return $data;
            }
        } catch (\Throwable $e) {

            $data['status'] = 'failed';
            $data['token'] = null;
            $data['refno'] =  null;

            return $data;
        }
    }

    public function verifyOTP(Request $request)
    {
        $tokenOTP = $request->token;
        $otpCode = $request->otp;

        try {
            // เชค open verifyOTP
                $body = [
                    'key' => "1795120423986985",
                    'secret' => "a10fb7d5cbe4cce5682faafa6846948a",
                    'token' => $tokenOTP,
                    'pin' => $otpCode,
                ];

                // $body = [
                //     'key' => "1774008287493544",
                //     'secret' => "6b17ac71d2dbdadef3845da4cc83f035",
                //     'msisdn' => $tel
                // ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                ])->post('https://otp.thaibulksms.com/v2/otp/verify', $body);

                if ($response->successful()) {
                    $res = $response->body();
                    $data = json_decode($res);
        
                    return $this->returnSuccess("เข้าสู่ระบบสำเร็จ",$data);
                } else {
                    return $this->returnErrorData(json_decode($response->body()), 404);
                }
        } catch (\Throwable $e) {
            return false;
        }
    }
}
