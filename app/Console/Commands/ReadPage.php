<?php

namespace App\Console\Commands;


use App\Services\YoutubenImport;
use Illuminate\Console\Command;

class ReadPage extends Command
{
    protected $signature = "youtuben:ReadPage";
    protected $description = "Read Page";

    public function handle()
    {

        $x = new YoutubenImport();
        $x->getChannelVideos([
            'channelId' => 'UC3ll4zd98mHTrfnPTJm3S0g'
        ]);

    }

}
