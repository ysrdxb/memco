<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DeliveryOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $deliveryOrder;
    protected $user;

    public function __construct($user, $deliveryOrder)
    {
        $this->user = $user;
        $this->deliveryOrder = $deliveryOrder;
    }
    
    // public function via($notifiable)
    // {
    //     return ['database'];
    // }

    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'type' => 'delivery_order',
            'user_id' => $this->user,
            'notifiable_id' => $this->deliveryOrder->id,
            'notifiable_type' => get_class($this->deliveryOrder),
            'data_message' => 'New delivery order created: ' . $this->deliveryOrder->title,
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'delivery_order',
            'user_id' => $this->user,
            'notifiable_id' => $this->deliveryOrder->id,
            'notifiable_type' => get_class($this->deliveryOrder),
            'data_message' => 'New delivery order created: ' . $this->deliveryOrder->title,
        ];
    }
    
}
