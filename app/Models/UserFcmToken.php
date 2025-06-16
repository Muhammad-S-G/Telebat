<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserFcmToken extends Model
{
    use HasFactory;

    protected $table = 'user_fcm_tokens';

    protected $fillable = [
        'user_id',
        'token_id',
        'fcm_token'
    ];

    protected $casts = [
        'created_at' => 'date: Y-m-d h:i a',
        'updated_at' => 'date: Y-m-d h:i a',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
