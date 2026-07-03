<?php

namespace App\Observers;

use App\Events\NotificationCreated;
use App\Models\Notification;

class NotificationObserver
{
    public function creating(Notification $notification): void
    {
        //
    }

    public function created(Notification $notification): void
    {
        NotificationCreated::dispatch($notification);
    }
    
    public function updated(Notification $notification): void
    {
        //
    }

    public function deleted(Notification $notification): void
    {
        //
    }
}