<?php

namespace App\Listeners;

use App\Mail\BlogPublished;
use App\Models\Contacts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBlogPublishedNotification
{
    public function handle($event)
    {
        Log::info('Listener is triggered');
        // all contacts
        $subscribers = Contacts::all();

        foreach ($subscribers as $subscriber) {
            // logging the email
            // Log::info('Sending email to ' . $subscriber->email);
            Mail::queue(new BlogPublished($event->post, $subscriber->email));
        }
    }
}
