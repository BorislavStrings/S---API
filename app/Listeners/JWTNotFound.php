<?php

namespace App\Listeners;

use App\Events\tymon.jwt.user_not_found;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JWTNotFound
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
     * @param  tymon.jwt.user_not_found  $event
     * @return void
     */
    public function handle(tymon.jwt.user_not_found $event)
    {
        //
    }
}
