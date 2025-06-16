<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
    protected $fillable = [
        'password_otp_code',
        'password_otp_expired_date',
        'verified_at'
    ];
}
