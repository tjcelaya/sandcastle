<?php

namespace App\Jobs;

use Log;
use App\Jobs\Base;
use App\Model\Contact;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DoTheJob extends Base implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $contact;

    public function __construct()
    {
    }

    public function handle()
    {
        Log::info('job handled');
    }
}
