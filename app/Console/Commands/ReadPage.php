<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

class ReadPage extends Command
{
    protected $signature = "youtuben:ReadPage";
    protected $description = "Read Page";

    public function handle()
    {

        # Use the Curl extension to query Google and get back a page of results
        $url = "http://www.youtube.com/get_video_info?video_id=9b5DlnsdV-Y";
        //zVYMDJq1oZM
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        $data = $this->splitOut($html);
        foreach (['adaptive_fmts', 'fflags', 'url_encoded_fmt_stream_map'] as $key) {
            $data[$key] = $this->splitOut($data[$key]);
        }
        $data['player_response'] = json_decode($data['player_response']);
        // print_r($data);return;
        // print_r($data['player_response']);return;
        $videoDetails = $data['player_response']->videoDetails;
        print_r([
            'title' => $videoDetails->title,
            'tags' => $videoDetails->keywords,
            'length' => $videoDetails->lengthSeconds,
            'small_img' => $videoDetails->thumbnail->thumbnails[0]->url,
            'med_img' => $videoDetails->thumbnail->thumbnails[1]->url,
            'big_img' => $videoDetails->thumbnail->thumbnails[2]->url,
            'views' => $videoDetails->viewCount,
        ]);


    }

    function splitOut($string)
    {
        $data = [];
        foreach (
            explode('&', $string)
            as $v) {
            list($key, $value) = explode('=', $v);
            $data[$key] = urldecode($value);
        };
        return $data;
    }

}