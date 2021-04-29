<?php

namespace App\Notifications;

use App\SentDockets;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SentDocketNotification extends Notification
{
    use Queueable;
    private $sentDocket;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sentDocket)
    {
        $this->sentDocket   =   $sentDocket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toSlack($notification){
        $sentDocket     =    $this->sentDocket;
        return (new SlackMessage)
            ->success()
            ->content("Docket Sent")
            ->attachment(function($attachment) use($sentDocket) {
               $attachment->title("Docket")
                   ->fields([
                        'Sender' => $sentDocket['sender_name'],
                        'Company' => $sentDocket['company_name'],
                        'Template' => $sentDocket['template_title']
                   ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
