<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class NotifyProjectStatusChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $projects = Project::all();
        
        // foreach ($projects as $project) {
        //     $originalStatus = $project->getOriginal('status');
        //     $currentStatus = $project->status;

        //     if ($originalStatus !== $currentStatus) {
        //         $message = "Project '{$project->name}' status changed from '$originalStatus' to '$currentStatus'";

        //         $httpClient = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        //         $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        //         $textMessageBuilder = new TextMessageBuilder($message);
        //         $response = $bot->pushMessage(env('LINE_GROUP_ID'), $textMessageBuilder);

        //         if ($response->isSucceeded()) {
        //             \Log::info("Message sent successfully: $message");
        //         } else {
        //             \Log::error("Failed to send message: " . $response->getRawBody());
        //         }
        //     }
        // }
    }
}
