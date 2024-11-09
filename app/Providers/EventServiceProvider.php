<?php

namespace App\Providers;

use App\Events\BlogPublished;
use App\Events\CampaignEmailEvent;
use App\Listeners\SendCampaignEmail;
use App\Listeners\SendBlogPublishedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BlogPublished::class => [
            SendBlogPublishedNotification::class,
        ],
        CampaignEmailEvent::class => [
            SendCampaignEmail::class,
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
    }
}
