<?php

namespace App\Listeners;

use App\Events\tymon.jwt.invalid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JWTInvalid
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
     * @param  tymon.jwt.invalid  $event
     * @return void
     */
    public function handle(tymon.jwt.invalid $event)
    {
        //
    }
}
