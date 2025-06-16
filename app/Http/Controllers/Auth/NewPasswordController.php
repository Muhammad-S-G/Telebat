<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use App\Traits\ResetPasswordOtp;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    use ResetPasswordOtp;

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'email_otp_code' => 'required|integer'
        ]);

        $user = User::whereEmail($validated['email'])->first();
        $ok = $this->verifyOtp($user, $validated['email_otp_code']);

        if (!$ok) {
            return message('try again please !!', 400);
        }
        return success(['reset_password' => route('password.reset')], 200);
    }


    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'password' => 'required|confirmed|string|min:8',
        ]);

        $user = User::whereEmail($validated['email'])->first();
        $password_otp = $user->passwordOtps()->latest()->first();

        if (!$password_otp || $password_otp->verified_at === null) {
            return error('Verify otp before reseting your password.', 400);
        }
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        $user->passwordOtps()->delete();
        return message('Your password has been reset.', 200);
    }
}
