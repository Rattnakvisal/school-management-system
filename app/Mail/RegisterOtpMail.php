<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public int|string $otp;

    public function __construct(int|string $otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Your registration OTP')
            ->view('emails.register-otp')
            ->with(['otp' => $this->otp]);
    }
}
