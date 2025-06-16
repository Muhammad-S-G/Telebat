<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ShipOrderNotification extends Notification
{
    use Queueable;

    protected $message;
    public function __construct(protected Order $order) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->fcm_tokens()->exists() ? ['mail', 'database', FcmChannel::class] : [];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('vendor.orders', ['store' => $this->order->store_id]);
        return (new MailMessage)
            ->line(__('messages.ship_order'))
            ->action('Show orders', $url)
            ->line('Thank you for using our application!');
    }


    public function toFcm($notifiable)
    {
        $this->message = __('messages.ship_order');

        return (new FcmMessage(notification: new FcmNotification(
            title: 'You got a new Order.',
            body: $this->message,
        )))
            ->data([
                'order_id' => (string)$this->order->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
                'apns' => [
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
            ]);
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'title' => __('messages.ship_order'),
            'message' => $this->message,
        ];
    }
}
