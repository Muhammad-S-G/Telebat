<?php

namespace App\Traits;

use App\Mail\OtpMail;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Util\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

trait GmailOtp
{

    protected int $minutes = 60;

    protected function fullfill(User $user, string $message)
    {
        $code = $this->generate(4);
        $this->set($user, $code);
        $this->send($user, $code, $message);
    }

    protected function verifyOtp(User $user, string $otp)
    {
        if (!Hash::check($otp, $user->email_otp_code) and $user->email_otp_expired_date >= Carbon::now()) {
            $user->verified_at = Carbon::now();
            $user->save();
            return true;
        }
        return false;
    }

    protected function generate(int $digits)
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    protected function set(User $user, int $otp)
    {
        $user->email_otp_code = $otp;
        $user->email_otp_expired_date = Carbon::now()->addMinutes($this->minutes);
        $user->save();
    }

    protected function send(User $user, int $otp, $message)
    {
        Mail::to($user->email)->send(new OtpMail($otp, $message));
    }
}
