<?php

namespace App\Listeners;

use App\Events\tymon.jwt.absent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JWTAbsent
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
     * @param  tymon.jwt.absent  $event
     * @return void
     */
    public function handle(tymon.jwt.absent $event)
    {
        //
    }
}
