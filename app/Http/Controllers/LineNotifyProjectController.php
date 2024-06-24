<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LineNotifyProjectController extends Controller
{
    public function NoticeLine($message_data)
    {
        $token = env('LINE_NOTIFY_ACCESS_TOKEN');
        $sendLine = $this->sendLine($token, $message_data);
    }
    public function sendLine($line_token, $text)
    {
        $sToken = $line_token;
        $sMessage = $text;

        $chOne = curl_init();
        curl_setopt($chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($chOne, CURLOPT_POST, 1);
        curl_setopt($chOne, CURLOPT_POSTFIELDS, "message=" . $sMessage);
        $headers = array('Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $sToken . '');
        curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($chOne);

        curl_close($chOne);
    }

    public function index(Request $request)
    {
        $employee_id = $request->query('id');
        $clientId = 'MWWSx4M3elQgRdroBDhXR2';
        $redirectUri = 'http://192.168.1.164/gorilla-api/public/api/line-notify/callback';
        $state = $employee_id;

        $authUrl = "https://notify-bot.line.me/oauth/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope=notify&state={$state}";

        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->query('code');
            $state = $request->query('state');

            DB::beginTransaction();
            $tokenUrl = 'https://notify-bot.line.me/oauth/token';
            $clientId = 'MWWSx4M3elQgRdroBDhXR2';
            $clientSecret = 'PTlorqFw30xexcsUsLOcD12YwCuz5QGzcA6OtzN9qza';
            $redirectUri = 'http://192.168.1.164/gorilla-api/public/api/line-notify/callback';

            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            $responseBody = $response->json();
            // dd($state);

            if (isset($responseBody['access_token'])) {
                $accessToken = $responseBody['access_token'];
                $item = EmployeeCredential::find($state);
                // dd($accessToken);
                $item->LCID = $accessToken;
                $item->save();

                DB::commit();
                $script = "<script>window.opener.postMessage('success', '*'); window.close();</script>";

                echo $script;

                exit;
            } else {
                DB::rollback();
                return redirect('/line-notify')->with('error', 'Failed to generate access token');
            }
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 500);
        }
    }
}
