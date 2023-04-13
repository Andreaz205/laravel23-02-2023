<?php

namespace App\Listeners\Email;

use App\Events\Email\VerifyNotificationEvent;
use App\Mail\VerificationNotification;
use App\Models\UserEmailCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HandleVerifyNotification implements ShouldQueue
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
     * @param  object  $event
     * @return void
     */
    public function handle(VerifyNotificationEvent $event)
    {
        $email = $event->email;
        $note = UserEmailCode::create(['email' => $email]);
        Log::info($note);
        Mail::to($email)->send(new VerificationNotification($note->code));
    }
}
