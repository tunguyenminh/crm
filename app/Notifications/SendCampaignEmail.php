<?php

namespace App\Notifications;

use App\Traits\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendCampaignEmail extends Notification implements ShouldQueue
{
    use Queueable, EmailSettings;

    private $mailSubject;
    private $mailContent;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $content)
    {
        $this->mailSubject = $subject;
        $this->mailContent = $content;
        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                    ->subject($this->mailSubject)
                    ->markdown('emails.send-campaign-email', ['mailContent' => $this->mailContent]);
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
