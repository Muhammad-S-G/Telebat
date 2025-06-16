<?php

namespace App\Traits;

use App\Mail\OtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Mail;

trait ResetPasswordOtp
{
    protected $minutes = 60;

    public function fullfill(User $user, string $message)
    {
        $otp = $this->generate(4);
        $this->set($user, $otp);
        $this->send($user, $otp, $message);
    }

    public function generate(int $digits)
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    public function set(User $user, int $otp)
    {
        $user->passwordOtps()->create([
            'password_otp_code' => Hash::make($otp),
            'password_otp_expired_date' => Carbon::now()->addMinutes($this->minutes)
        ]);
    }

    public function send(User $user, int $otp, $message)
    {
        Mail::to($user)->send(new OtpMail($otp, $message));
    }





    public function verifyOtp(User $user, $otp)
    {
        $password_otp = $user->passwordOtps()->latest()->first();
        if (
            $password_otp &&
            Hash::check($otp, $password_otp->password_otp_code)
            &&
            $password_otp->password_otp_expired_date >= Carbon::now()
        ) {
            $password_otp->update(['verified_at' => Carbon::now()]);
            return true;
        }
        return false;
    }
}
