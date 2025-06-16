<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResetPasswordOtp;
use Illuminate\Http\Request;

class PasswordResetLinkController extends Controller
{
    use ResetPasswordOtp;
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        $user = User::whereEmail($validated['email'])->first();
        $this->fullfill($user, 'Please use this code to reset your Password.');
    }
}
