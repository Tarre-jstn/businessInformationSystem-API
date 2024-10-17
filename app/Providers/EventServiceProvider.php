<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\BusinessNameUpdated;
use App\Listeners\SendBusinessNameChangeNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BusinessNameUpdated::class => [
            SendBusinessNameChangeNotification::class,
        ],    
    ];

    

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
