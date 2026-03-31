<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $expiresInMinutes;
    public $purpose;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code, int $expiresInMinutes = 10, string $purpose = 'login')
    {
        $this->code = $code;
        $this->expiresInMinutes = $expiresInMinutes;
        $this->purpose = $purpose;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $subject = $this->purpose === 'email_change'
            ? __('messages.auth.two_factor_email_subject_email_change')
            : __('messages.auth.two_factor_email_subject_login');

        return $this->subject($subject)
                    ->view('emails.verification-code')
                    ->with([
                        'code' => $this->code,
                        'expiresInMinutes' => $this->expiresInMinutes,
                        'purpose' => $this->purpose,
                    ]);
    }
}
