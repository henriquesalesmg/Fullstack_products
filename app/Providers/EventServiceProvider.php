<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\ProductCreated;
use App\Listeners\SendProductCreatedNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * O array de eventos e seus listeners.
     */
    protected $listen = [
        ProductCreated::class => [
            SendProductCreatedNotification::class,
        ],
        \App\Events\UserRegistered::class => [
            \App\Listeners\SendUserRegisteredNotification::class,
        ],
    ];
}
