<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    public function webhook(Request $request)
    {
        $events = $request->events;

        foreach ($events as $event) {
            if (isset($event['source']['groupId'])) {
                $groupId = $event['source']['groupId'];
                Log::info('Group ID: ' . $groupId);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
