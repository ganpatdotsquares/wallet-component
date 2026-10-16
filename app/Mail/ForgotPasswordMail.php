<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $user;
    public $template;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->user = $data['user'];
        $this->template = $data['template'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->buildMessage($this->template);
        $subject = $message['subject'];
        $content = $message['content'];

        return $this->view('emails.api.common')
        ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
        ->replyTo(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
        ->subject($subject)
        ->with(['content' => $content]);
    }

    /**
     * Get the content, subject
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function buildMessage($email_type = null)
    {
        if (!$email_type) {
            return false;
        }

        $emailTemplate = EmailTemplate::with('emailTemplateLangs')
            ->whereSlug($email_type)
            ->where('status', 1)
            ->first();

        if (!$emailTemplate) {
            return false;
        }

        $emailTemplateLang = $emailTemplate->emailTemplateLangs->first();

        if (!$emailTemplateLang) {
            return false;
        }

        $subject = $emailTemplateLang->subject;
        $content = $emailTemplateLang->description;
        $fullName = $this->user->first_name.' '.$this->user->last_name;

        $subject = str_replace('%APP_NAME%', env('APP_NAME'), $subject);
        $content = str_replace('%APP_NAME%', env('APP_NAME'), $content);
        $content = str_replace('%USER_NAME%', $fullName, $content);
        $content = str_replace('%OTP%', $this->user->forgot_password_token, $content);

        return [
            'content' => $content,
            'subject' => $subject
        ];
    }

}
