<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;
use DB;

class AdminMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The template instance.
     *
     * @var template
     */
    public $template;

    /**
     * The replacement instance.
     *
     * @var replacement
     */
    public $replacement;
    public $subject;
    public $bodyhtml;
    public $to;
    public $attachment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($replacementVars, $attachment = null)
    {
      
        if (!empty($replacementVars['hooksVars'])) {
            foreach ($replacementVars['hooksVars'] as $hook => $var) {
                $replacement['##' . $hook . '##'] = $var;
            }
        }
        $replacement['##APP_NAME##'] = config('GET.system_application_name');
        $replacement['##system_application_name##'] = config('GET.system_application_name');
        $this->template = $replacementVars['template'];
        $this->replacement = $replacement;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->buildMessage($this->template);
        
        if ($message) {
            $subject = $message['subject'];
            $content = $message['message'];
        } else {
            $subject = $this->subject;
            $content = $this->bodyhtml;
        }

        $mail = $this->view('emails.master')
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->replyTo('ds25@24livehost.com', 'CustomApp')
            ->subject($subject)
            ->with(['content' => $content]);
        if ($this->attachment && file_exists($this->attachment)) {
            $mail->attach($this->attachment);
        }
        return $mail;
    }

    public function buildMessage($email_type = null)
    {
        if (!$email_type) {
            return false;
        }
        $emailTemplate = DB::table('email_templates')->where('slug', $email_type)->first();
        if (empty($emailTemplate)) {
            return false;
        }

        $fullUrl = \App::make('url')->to('/');
        $replacement = $this->replacement;
        $message_body = strtr($emailTemplate->description, $replacement);
        $subject = $emailTemplate->subject;
        $subject = strtr($subject, $replacement);
        return $message = ['message' => $message_body, 'subject' => $subject];
    }
}
