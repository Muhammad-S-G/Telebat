<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Phone extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'phone_number_verified_at',
        'phone_number_otp_code',
        'phone_number_otp_expired_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
