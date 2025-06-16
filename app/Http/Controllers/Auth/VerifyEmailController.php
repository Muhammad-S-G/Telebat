<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals(
            (string)$request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            return error('Invalid verification link.', 403);
        }

        if ($user->hasVerifiedEmail()) {
            return message('Email already verified.', 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return message('Email verified successfully.', 200);
    }
}
