<?php

namespace App\Services;

use App\Jobs\MagicJob;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QueueService
{

    use DispatchesJobs, Queueable;

    const DEFAULT_QUEUE = 'youtuben';

    public function sendToQueue($class, $method, $args = [], $queue = null, $runNow = false)
    {

        if ($queue == null) {
            $queue = self::DEFAULT_QUEUE;
        }

        $job = new MagicJob($class, $method, $args);

        if ($runNow === true) {
            $this->dispatchNow($job->onQueue($queue));
        } else {
            $this->dispatch($job->onQueue($queue));
        }
    }
}
