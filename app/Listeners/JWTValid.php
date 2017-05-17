<?php

namespace App\Listeners;

use App\Events\tymon.jwt.valid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JWTValid
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
     * @param  tymon.jwt.valid  $event
     * @return void
     */
    public function handle(tymon.jwt.valid $event)
    {
        //
    }
}
