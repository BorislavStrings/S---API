<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\tymon.jwt.absent' => [
            'App\Listeners\JWTAbsent',
        ],
        'App\Events\tymon.jwt.expired' => [
            'App\Listeners\JWTExpired',
        ],
        'App\Events\tymon.jwt.invalid' => [
            'App\Listeners\JWTInvalid',
        ],
        'App\Events\tymon.jwt.user_not_found' => [
            'App\Listeners\JWTNotFound',
        ],
        'App\Events\tymon.jwt.valid' => [
            'App\Listeners\JWTValid',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
