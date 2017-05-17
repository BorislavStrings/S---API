<?php

namespace App\Listeners;

use App\Events\tymon.jwt.expired;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JWTExpired
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  tymon.jwt.expired  $event
     * @return void
     */
    public function handle(tymon.jwt.expired $event)
    {
        //
    }
}
