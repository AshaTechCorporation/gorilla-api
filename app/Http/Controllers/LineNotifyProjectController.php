<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeCredential;
use App\Models\Influencer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LineNotifyProjectController extends Controller
{
    public function NoticeLine($message_data)
    {
        //Legacy
        $token = env('LINE_NOTIFY_ACCESS_TOKEN');
        $sendLine = $this->sendLine($token, $message_data);
    }

    public function NoticebyClient($Linetoken,$data,$round)
    {
        $token = $Linetoken;
        $project = $data->product_items->products->projects->name?? 'ไม่มีค่า';
        $Influencer = Influencer::find($data->influencer_id)->fullname?? 'ไม่มีค่า';
        $draftstatus = $round == 0 ? "draft1" : ($round == 1 ? "draft2" : ($round == 2 ? "draft3" : ($round == 3 ? "post" : ($round == 4 ? "payment" : "Unknown"))));
        if($round < 4){
            $toround = $round + 1;
        }else{
            $toround = $round;
        }
        $draftstatus2 = $toround == 0 ? "draft1" : ($toround == 1 ? "draft2" : ($toround == 2 ? "draft3" : ($toround == 3 ? "post" : ($toround == 4 ? "payment" : "Unknown"))));
        switch ($round) {
            case 0:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 1:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 2:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 3:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 4:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงเป็น " . $draftstatus . "\n";
            default:
                $message_data = "เกิดข้อผิดพลาดในการแจ้งเตือน!!!";
        }
        $sendLine = $this->sendLine($token, $message_data);
    }

    public function NoticebyEmployee($Linetoken,$data,$round)
    {
        $token = $Linetoken;
        $project = $data->product_items->products->projects->name?? 'ไม่มีค่า';
        $Influencer = Influencer::find($data->influencer_id)->fullname?? 'ไม่มีค่า';
        $draftstatus = $round == 0 ? "draft1" : ($round == 1 ? "draft2" : ($round == 2 ? "draft3" : ($round == 3 ? "post" : ($round == 4 ? "payment" : "Unknown"))));
        if($round < 4){
            $toround = $round + 1;
        }else{
            $toround = $round;
        }
        $draftstatus2 = $toround == 0 ? "draft1" : ($toround == 1 ? "draft2" : ($toround == 2 ? "draft3" : ($toround == 3 ? "post" : ($toround == 4 ? "payment" : "Unknown"))));
        switch ($round) {
            case 0:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 1:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 2:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 3:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงจาก" . $draftstatus . " เป็น " . $draftstatus2 . "\n";
                break;
            case 4:
                $message_data =
                "แจ้งเตือนการเปลี่ยนสถานะดราฟ" . "\n" .
                "ในโปรเจ็ค " . $project . "\n" .
                "Influencer " . $Influencer . "\n" .
                "โดยพนักงานรหัส : " . $data->ecode . "\n" .
                "สถานะมีการเปลี่ยนแปลงเป็น " . $draftstatus . "\n";
            default:
                $message_data = "เกิดข้อผิดพลาดในการแจ้งเตือน!!!";
        }
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
        $redirectUri = 'http://localhost/gorilla-api/public/api/line-notify/callback';
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
            $redirectUri = 'http://localhost/gorilla-api/public/api/line-notify/callback';

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
                $item->LCID = md5($accessToken);
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
