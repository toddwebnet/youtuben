<?php

namespace App\Services\Api;


use Alaouy\Youtube\Youtube;

class YoutubeApi extends Youtube
{
    public function __construct()
    {
        parent::__construct(env('YOUTUBE_API_KEY'));
        $this->APIs = array_merge($this->APIs, [
            'subscriptions.list' => 'https://www.googleapis.com/youtube/v3/subscriptions',
        ]);
    }


    public function getChannelId($channelName){
        $data = $this->getChannelByName($channelName);
        return $data->id;
    }

    public function getSubscriptions($channelId, $part = ['snippet'])
    {
        $API_URL = $this->getApi('subscriptions.list');
        $params = [
            'channelId' => $channelId,
            'key' => $this->youtube_key,
            'part' => implode(', ', $part),
            'maxResults' => 50,
            'pageToken' => 'CGQQAA',

        ];

        $apiData = $this->api_get($API_URL, $params);
        print_r(json_decode($apiData));
        // return $this->decodeMultiple($apiData);
    }
}