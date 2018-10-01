<?php

namespace App\Services\Api;

use Alaouy\Youtube\Youtube;

class YoutubeApi extends Youtube
{
    const MAXRESULTS = 25;

    public function __construct()
    {
        parent::__construct(env('YOUTUBE_API_KEY'));
        $this->APIs = array_merge($this->APIs, [
            'subscriptions.list' => 'https://www.googleapis.com/youtube/v3/subscriptions',
            'channel.listVideos' => 'https://www.googleapis.com/youtube/v3/search'
        ]);
    }

    public function getChannelId($channelName)
    {
        $data = $this->getChannelByName($channelName);
        return $data->id;
    }

    public function getSubscriptions($channelId, $part = ['snippet'])
    {
        $data = [];
        $page = null;
        do {
            $content = $this->getSubscriptionData($channelId, $part, $page);
            if (!property_exists($content, 'items')) {
                print_r($content);
                die();
            }
            $data = array_merge($data, $content->items);
            if (property_exists($content, 'nextPageToken')) {
                $page = $content->nextPageToken;
            } else {
                $page = null;
            }

        } while ($page != null);
        return $data;
    }

    private function getSubscriptionData($channelId, $part, $page = null)
    {
        $API_URL = $this->getApi('subscriptions.list');
        $params = [
            'channelId' => $channelId,
            'key' => $this->youtube_key,
            'part' => implode(', ', $part),
            'maxResults' => self::MAXRESULTS
        ];
        if ($page !== null) {
            $params['pageToken'] = $page;
        }

        return json_decode($this->api_get($API_URL, $params));
    }


    public function listAllChannelVideos($channelId)
    {
        $nextPage = null;
        $maxResults = 50;
        $order = 'date';
        $part = ['id', 'snippet'];
        $params = [
            'type' => 'video',
            'channelId' => $channelId,
            'part' => implode(', ', $part),
            'maxResults' => $maxResults,
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }
        $data = [];
        $lastDate = null;
        $rows = 0;
        $dontStop = true;
        do {
            $rows++;
            if ($lastDate !== null) {
                $params['publishedBefore'] = $lastDate;
            }
            $results = $this->searchAdvanced($params, false);
            try {
                foreach ($results as $item) {

                    $videoId = $item->id->videoId;
                    if (!in_array($videoId, $data)) {
                        $data[] = $videoId;
                    }
                    $lastDate = $item->snippet->publishedAt;

                }
            } catch (\Exception $e) {
                // do nothing
            }

        } while (!array_key_exists('publishedBefore', $params) || $lastDate != $params['publishedBefore']);

        return $data;
    }


}
