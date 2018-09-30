<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MagicJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $class;
    protected $method;
    protected $args;

    public function __construct($class, $method, $args)
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;
    }

    public function handle()
    {
        $class = $this->class;
        $method = $this->method;
        $args = $this->args;

        app()->make($class)->$method($args);
    }
}
