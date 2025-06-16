<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $guard = 'sanctum';

    protected $fillable = [
        'email',
        'email_verified_at',
        'password',
        'first_name',
        'last_name',
        'profile_picture',
        'latitude',
        'longitude',
        'verified_at',
        'password_confirmed_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    public function storeProfilePicture($file)
    {
        if ($file) {

            $oldImagePath = $this->getOriginal('profile_picture');
            $path = $file->store('profile_pictures', 'public');
            $this->update(['profile_picture' => $path]);

            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            return $path;
        }
        return null;
    }

    public function phone()
    {
        return $this->hasOne(Phone::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'vendor_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function passwordOtps()
    {
        return $this->hasMany(PasswordOtp::class);
    }

    public function fcm_tokens()
    {
        return $this->hasMany(UserFcmToken::class);
    }

    public function getDeviceTokens()
    {
        return $this->fcm_tokens()
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function routeNotificationForFcm()
    {
        return $this->getDeviceTokens();
    }
}
