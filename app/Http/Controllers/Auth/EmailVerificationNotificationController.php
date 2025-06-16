<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $data = $validator->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user->hasVerifiedEmail()) {
            return message('Email already verified.');
        }

        $user->sendEmailVerificationNotification();
        return message('Verification link sent!');
    }
}
