<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\ChannelStats;
use App\Models\Subscribers;

class DataImportService
{
    public function saveChannel($args)
    {
        $downloadId = $args['downloadId'];
        $ownerChannelId = $args['ownerChannelId'];
        $dataStream = $args['dataStream'];

        $snippet = $dataStream->snippet;
        $channelId = $snippet->channelId;

        $subscriber = Subscribers::create([
            'download_id' => $downloadId,
            'owner_channel_id' => $ownerChannelId,
            'channel_id' => $channelId
        ]);

        try {
            $defaultImage = $snippet->thumbnails->default->url;
        } catch (\Exception $e) {
            $defaultImage = null;
        }
        try {
            $mediumImage = $snippet->thumbnails->medium->url;
        } catch (\Exception $e) {
            $mediumImage = null;
        }
        try {
            $highImage = $snippet->thumbnails->high->url;
        } catch (\Exception $e) {
            $highImage = null;
        }
        $channelData = [
            'youtube_channel_id' => $channelId,
            'title' => $snippet->title,
            'description' => $snippet->description,
            'custom_url' => (property_exists($snippet, 'customUrl'))?$snippet->customUrl:null,
            'published_at' => date("Y-m-d G:h:s", strtotime($snippet->publishedAt)),
            'default_img_url' => $defaultImage,
            'medium_img_url' => $mediumImage,
            'high_img_url' => $highImage,
        ];
        // print_r($channelData);
        if (Channel::where('youtube_channel_id', $channelId)->count() > 0) {
            print "\nupdating {$channelId} \n";
            Channel::where('youtube_channel_id', $channelId)
                ->update($channelData);
        } else {
            print "\nadding  {$channelId} \n";
            Channel::create($channelData);
        }

        // $channel = Channel::where('youtube_channel_id', $channelId)->first();

        $class = YoutubenImport::class;
        $method = 'getChannelStats';
        $args = [
            'downloadId' => $downloadId,
            'channelId' => $channelId
        ];

        $queueService = app()->make(QueueService::class);
        $queueService->sendToQueue($class, $method, $args);
    }

    public function saveChannelStats($args){
        $dataPacket = $args['dataPackedt'];
        $channelId = $dataPacket['channel_id'];
        $downloadId = $dataPacket['download_id'];
        ChannelStats::insert($dataPacket);
        ChannelStats::where([
            ['channel_id',$channelId],
            ['latest',1]
        ])->update(['latest' => false]);
        ChannelStats::where([
            ['channel_id',$channelId],
            ['download_id',$downloadId]
        ])
            ->update(['latest'=> true]);

    }
}
