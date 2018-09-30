<?php

namespace App\Console\Commands;

use App\Services\Api\YoutubeApi;
use App\Services\QueueService;
use App\Services\YoutubenImport;
use Illuminate\Console\Command;

class GetSubs extends Command
{
    protected $signature = 'youtuben:getSubs';
    protected $description = 'Get Subscriptions';

    public function handle()
    {

        // put this into a queue
        $class = YoutubenImport::class;
        $method = 'importSubscriptions';
        $args = ['channelId' => env('YOUTUBE_CHANNEL_ID')];

        $queueService = app()->make(QueueService::class);
        $queueService->sendToQueue($class, $method, $args);
    }
}
