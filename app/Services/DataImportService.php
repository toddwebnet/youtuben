<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\ChannelStats;
use App\Models\Subscribers;
use App\Models\Video;
use App\Models\VideoStats;
use App\Models\VideoTag;

class DataImportService
{
    public function saveChannel($args)
    {
        $downloadId = $args['downloadId'];
        $ownerChannelId = $args['ownerChannelId'];
        $dataStream = $args['dataStream'];

        $snippet = $dataStream->snippet;
        $channelId = $snippet->resourceId->channelId;

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
            'custom_url' => (property_exists($snippet, 'customUrl')) ? $snippet->customUrl : null,
            'published_at' => date("Y-m-d G:h:s", strtotime($snippet->publishedAt)),
            'default_img_url' => $defaultImage,
            'medium_img_url' => $mediumImage,
            'high_img_url' => $highImage,
        ];

        if (Channel::where('youtube_channel_id', $channelId)->count() > 0) {
            Channel::where('youtube_channel_id', $channelId)
                ->update($channelData);
        } else {
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

    public function saveChannelStats($args)
    {
        $dataPacket = $args['dataPacket'];
        $channelId = $dataPacket['channel_id'];
        $downloadId = $dataPacket['download_id'];
        ChannelStats::create($dataPacket);


        ChannelStats::where([
            ['channel_id', $channelId],
            ['latest', 1]
        ])->update(['latest' => false]);
        ChannelStats::where([
            ['channel_id', $channelId],
            ['download_id', $downloadId]
        ])
            ->update(['latest' => true]);

    }

    function saveVideoData($args)
    {
        $downloadId = $args['downloadId'];
        $videoId = $args['videoId'];
        $video = $args['video'];
        $tags = $args['tags'];
        $stats = $args['stats'];

        $insertFlag = $this->processVideo($videoId, $video);
        $video = Video::where('youtube_video_id', $videoId)->first();
        $this->processTags($video->id, $tags, $insertFlag);
        $this->processStats($downloadId, $video->id, $stats);

    }

    private function processStats($downloadId, $videoId, $stats)
    {
        $stats = (object)$stats;
        $data = [
            'download_id' => $downloadId,
            'video_id' => $videoId,
            'views' => $stats->views,
            'likes' => $stats->likes,
            'dislikes' => $stats->dislikes,
            'favorites' => $stats->favorites,
            'comment_count' => $stats->comment_count,
        ];
        VideoStats::create($data);

        VideoStats::where([
            ['video_id', $videoId],
            ['latest', 1]
        ])->update(['latest' => false]);
        VideoStats::where([
            ['video_id', $videoId],
            ['download_id', $downloadId]
        ])
            ->update(['latest' => true]);
    }

    private function processVideo($videoId, $video)
    {
        $insertFlag = false;
        if (Video::where('youtube_video_id', $videoId)->count() > 0) {
            Video::where('youtube_video_id', $videoId)
                ->update($video);
        } else {

            Video::create($video);
            $insertFlag = true;
        }
        return $insertFlag;
    }

    private function processTags($videoId, $tags, $insertFlag)
    {
        foreach ($tags as $tag) {

            if ($insertFlag || VideoTag::where([
                    ['video_id', $videoId],
                    ['tag', $tag]
                ])->count() == 0) {
                VideoTag::create([
                    'video_id' => $videoId,
                    'tag' => $tag,
                ]);
            }
            VideoTag::where('video_id', $videoId)
                ->whereNotIn('tag', $tags)
                ->delete();
        }
    }
}
