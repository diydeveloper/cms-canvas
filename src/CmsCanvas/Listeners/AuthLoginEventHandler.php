<?php

namespace CmsCanvas\Listeners;

use DateTime;
use Illuminate\Auth\Events\Login;

class AuthLoginEventHandler
{

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $event->user->last_login = new DateTime;
        $event->user->save();
    }

}