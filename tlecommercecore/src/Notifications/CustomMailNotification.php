<?php

namespace Plugin\TlcommerceCore\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomMailNotification extends Notification implements ShouldQueue
{
    use Queueable;


    private $data;
    private $keywords;
    private $subject;
    private $template_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data, $keywords, $subject, $template_id)
    {
        $this->data = $data;
        $this->keywords = $keywords;
        $this->subject = $subject;
        $this->template_id = $template_id;
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
            ->subject($this->subject)
            ->markdown('core::base.email.email_templates.global_mail_template', ['template_id' => $this->template_id, 'data' => $this->data, 'keywords' => $this->keywords]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [];
    }
}
