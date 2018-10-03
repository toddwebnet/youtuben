<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Download;
use App\Services\Api\YoutubeApi;

class YoutubenImport
{
    const SUB_TYPE = 'subs';
    const CHANNEL_TYPE = 'channel';

    /**
     * call within queue
     * @param $args
     */
    public function importSubscriptions($args)
    {
        $channelId = $args['channelId'];
        $queueService = app()->make(QueueService::class);

        $download = Download::create([
            'type' => self::SUB_TYPE
        ]);
        /** @var YoutubeApi $youtube */
        $youtube = app()->make(YoutubeApi::class);

        $downloadId = $download->id;
        foreach ($youtube->getSubscriptions($channelId, ['id', 'snippet']) as $sub) {
            if ($sub->kind == 'youtube#subscription') {

                $args = [
                    'ownerChannelId' => $channelId,
                    'downloadId' => $downloadId,
                    'dataStream' => $sub
                ];
                $queueService->sendToQueue(DataImportService::class, 'saveChannel', $args);
            }
        }
    }

    public function getChannelStats($args)
    {
        $channelId = $args['channelId'];
        $downloadId = $args['downloadId'];

        /** @var YoutubeApi $youtube */
        $youtube = app()->make(YoutubeApi::class);
        $stats = $youtube->getChannelById($channelId, false, ['statistics']);
        $stats = $stats->statistics;

        $channel = Channel::where('youtube_channel_id', $channelId)->firstOrFail();

        $dataPacket = [
            'download_id' => $downloadId,
            'channel_id' => $channel->id,
            'views' => $stats->viewCount,
            'comment_count' => $stats->commentCount,
            'subscriber_count' => $stats->subscriberCount,
            'video_count' => $stats->videoCount,
        ];

        $class = DataImportService::class;
        $method = "saveChannelStats";
        $args = [
            'dataPacket' => $dataPacket
        ];

        /** @var QueueService $queuService */
        $queueService = app()->make(QueueService::class);
        $queueService->sendToQueue($class, $method, $args);
    }

    public function getChannelVideos($args)
    {
        $channelId = $args['channelId'];
        /** @var YoutubeApi $youtube */
        $youtube = app()->make(YoutubeApi::class);

        $download = Download::create([
            'type' => self::SUB_TYPE
        ]);
        $videos = $youtube->listAllChannelVideos($channelId);
        print "videos: " . count($videos) . "\n";
        $i = 0;
        foreach ($videos as $videoId) {

            if ($i % 60 == 0) {
                print "\n {$i} ";
            }
            $i++;
            print ".";
            $class = YoutubenImport::class;
            $method = 'getVideoDetails';
            $args = [
                'videoId' => $videoId,
                'downloadId' => $download->id,
            ];
            /** @var QueueService $queuService */
            $queueService = app()->make(QueueService::class);
            $queueService->sendToQueue($class, $method, $args);
        }
        print "\n";
    }

    public function getVideoDetails($args)
    {

        $videoId = $args['videoId'];
        $downloadId = $args['downloadId'];

        /** @var YoutubeApi $youtube */
        $youtubeApi = app()->make(YoutubeApi::class);

        $content = $youtubeApi->getVideoInfo($videoId);
        $snippet = $content->snippet;

        $channel = Channel::where('youtube_channel_id', $snippet->channelId)
            ->firstOrFail();

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

        $video = [
            'channel_id' => $channel->id,
            'youtube_video_id' => $videoId,
            'title' => $snippet->title,
            'descr' => $snippet->description,
            'default_img_url' => $defaultImage,
            'medium_img_url' => $mediumImage,
            'high_img_url' => $highImage,
            'player_html' => $content->player->embedHtml,
            'published_at' => date("Y-m-d G:h:s", strtotime($snippet->publishedAt)),
            'duration' => $content->contentDetails->duration
        ];

        $tags = (property_exists($snippet, 'tags'))
            ? $this->trimTags($snippet->tags)
            : [];

        $statistics = $content->statistics;
        $stats = [
            'views' => getProperty($statistics, 'viewCount', 0),
            'likes' => getProperty($statistics, 'likeCount', 0),
            'dislikes' => getProperty($statistics, 'dislikeCount', 0),
            'favorites' => getProperty($statistics, 'favoriteCount', 0),
            'comment_count' => getProperty($statistics, 'commentCount', 0),
        ];

        $class = DataImportService::class;
        $method = 'saveVideoData';
        $args = [
            'downloadId' => $downloadId,
            'videoId' => $videoId,
            'video' => $video,
            'tags' => $tags,
            'stats' => $stats
        ];

        /** @var QueueService $queuService */
        $queueService = app()->make(QueueService::class);
        $queueService->sendToQueue($class, $method, $args);
    }

    private function trimTags(array $tags)
    {
        foreach ($tags as &$tag) {
            if (strlen($tag) > 255) {
                $tag = substr($tag, 0, 255);
            }
        }
        return $tags;
    }
}
