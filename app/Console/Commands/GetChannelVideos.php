<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Services\Api\YoutubeApi;
use App\Services\QueueService;
use App\Services\YoutubenImport;
use Illuminate\Console\Command;

class GetChannelVideos extends Command
{
    protected $signature = 'youtuben:getChannelVideos';
    protected $description = 'Get Channel Videos';

    public function handle()
    {
        $channels = Channel::all();
        $t = count($channels);
        $i = 0;
        foreach ($channels as $channel) {
            $i++;
            if($i<2){continue;}
            print "\n{$i}/{$t} - " . $channel->youtube_channel_id . "\n";
            $class = YoutubenImport::class;
            $method = "getChannelVideos";
            $args = [
                'channelId' => $channel->youtube_channel_id
            ];
            /** @var QueueService $queuService */
            $queueService = app()->make(QueueService::class);
            $queueService->sendToQueue($class, $method, $args);
        }
        return;

    }
}