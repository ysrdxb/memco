<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class MaterialRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $materialRequest;

    public function __construct($materialRequest)
    {
        $this->materialRequest = $materialRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'material_request',
            'notifiable_id' => $this->materialRequest->id,
            'notifiable_type' => get_class($this->materialRequest),
            'message' => 'New material request created: ' . $this->materialRequest->title,
        ];
    }
}
