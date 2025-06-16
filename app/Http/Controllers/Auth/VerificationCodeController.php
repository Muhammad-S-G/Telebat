<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyPhoneNumberRequest;
use App\Models\Phone;
use App\Models\User;
use App\Traits\SmsOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerificationCodeController extends Controller
{
    use SmsOtp;

    public function sendCode(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'exists:phones,phone_number']
        ]);

        $phone = Phone::where('phone_number', $request->phone_number)->first();
        if (!$phone || !$user = $phone->user) {
            return error('User not found', 404);
        }
        return $this->fullfill($user, "Please use the code to verify your phone number !!", $request->phone_number);
    }

    public function verify(VerifyPhoneNumberRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();
        $ok = $this->verifyOtp($user, $validated['phone_number_otp_code']); // ok holds true or false

        if (!$ok) {
            return error('try agian please !!', 400);
        }

        $user->update([
            'verified_at' => Carbon::now()
        ]);

        return success(['user' => $user->fresh()], 200, 'user has been verified successfully');
    }
}
