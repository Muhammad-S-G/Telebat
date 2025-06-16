<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $status;
    protected $message;
    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

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
        $url = route('show.order', ['order' => $this->order->id]);
        return (new MailMessage)
            ->line(__('messages.order_status_changed'))
            ->action('Show Order', $url)
            ->line('Thank you for using our application!');
    }


    public function toFcm($notifiable): FcmMessage
    {
        $statusMessages = [
            'en' => [
                'approved' => 'Your order has been approved and is being processed.',
                'delivering' => 'Your order is out for delivery!',
                'delivered' => 'Your order has been delivered. Enjoy!'
            ],
            'ar' => [
                'approved' => 'تم قبول طلبك وجاري معالجته.',
                'delivering' => 'طلبك في الطريق إليك!',
                'delivered' => 'تم توصيل طلبك. نتمنى لك تجربة طيبة!'
            ]
        ];

        $locale = app()->getLocale();
        $message = $statusMessages[$locale];
        $this->message = $message[$this->status] ?? "Your Order status has been updated to {$this->status}.";

        $titles = [
            'en' => 'Order Status updated.',
            'ar' => 'تم تحديث حالة الطلب'
        ];

        return (new FcmMessage(notification: new FcmNotification(
            title: $titles[$locale] ?? $titles['en'],
            body: $this->message,
        )))
            ->data([
                'order_id' => (string)$this->order->id,
                'status' => $this->status,
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
            'order_status' => $this->status,
            'title' => __('messages.order_status_changed'),
            'message' => $this->message,
        ];
    }
}
