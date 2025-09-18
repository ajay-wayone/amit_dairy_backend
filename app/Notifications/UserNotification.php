<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $data;

    public function __construct($title, $message, $data = [])
    {
        $this->title   = $title;
        $this->message = $message;
        $this->data    = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // ya 'mail' agar email bhejna ho
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }
}
