<?php

namespace Core\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnStatusUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->mailData['subject'])->view('core::base.email.email_templates.global_mail_template', ['template_id' => $this->mailData['template_id'], 'data' => $this->mailData, 'keywords' => $this->mailData['keywords']]);
    }
}
