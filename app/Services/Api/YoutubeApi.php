<?php

namespace App\Services\Api;

use Alaouy\Youtube\Youtube;

class YoutubeApi extends Youtube
{
    const MAXRESULTS = 10;

    public function __construct()
    {
        parent::__construct(env('YOUTUBE_API_KEY'));
        $this->APIs = array_merge($this->APIs, [
            'subscriptions.list' => 'https://www.googleapis.com/youtube/v3/subscriptions',
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
            $page = null;
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

}
