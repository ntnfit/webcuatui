<?php

namespace App\Listeners;

use App\Events\CampaignEmailEvent;
use App\Mail\CampaignEmail;
use App\Models\Contacts;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CampaignEmailEvent $event): void
    {
        $subscribers = Contacts::all();
        foreach ($subscribers as $subscriber) {
            Mail::queue(new CampaignEmail($subscriber->email));
        }
    }
}
