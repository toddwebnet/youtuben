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
            'view_count' => $stats->viewCount,
            'comment_count' => $stats->commentCount,
            'subscriber_count' => $stats->subscriberCount,
            'video_count' => $stats->videoCount,
        ];
        $class = DataImportService::class;
        $method = "saveChannelStats";
        $args = [
            'dataPackedt' => $dataPacket
        ];

        /** @var QueueService $queuService */
        $queueService = app()->make(QueueService::class);
        $queueService->sendToQueue($class, $method, $args);
    }
}
