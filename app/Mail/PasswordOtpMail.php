<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public int|string $otp;

    /**
     * Create a new message instance.
     */
    public function __construct(int|string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your password reset OTP')
            ->view('emails.password-otp')
            ->with(['otp' => $this->otp]);
    }
}
