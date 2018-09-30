<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Youtube;
use App\Services\Api\YoutubeApi;
use Illuminate\Console\Command;

class GetSubscriber extends Command
{
    protected $signature = 'youtuben:getSubscriber';
    protected $description = 'Get Subscriber';

    public function handle()
    {
        $youtube = new YoutubeApi();
        // $channelId = $youtube->getChannelId('fotm1');
        $channelId = env('YOUTUBE_CHANNEL_ID');
        $channel = $youtube->getSubscriptions($channelId, ['contentDetails']);
        dump($channel);

    }
}