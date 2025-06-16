<?php

namespace App\Models;

use App\Observers\PaymentRequestObserver;
use App\Traits\CreatedAtDescScopeTrait;
use App\Traits\DateFormatTrait;
use App\Traits\HasArabicTrans;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;



class PaymentRequest extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, HasArabicTrans, DateFormatTrait, CreatedAtDescScopeTrait;

    protected $fillable = [
        'payable_id',
        'payable_type',
        'user_id',
        'stripe_payment_intent_id',
        'title',
        'description',
        'price',
        'currency',
        'status'
    ];

    protected $translatable = [
        'title',
        'description'
    ];

    protected $casts = [
        'title'       => 'array',
        'description' => 'array',
    ];



    public static function getTranslatable()
    {
        return [
            'title',
            'description'
        ];
    }


    public static function status()
    {
        return new class {
            public $pending = 'PENDING';
            public $completed = 'COMPLETED';
            public $canceled = 'CANCELED';
            public function all()
            {
                return [
                    self::$pending,
                    self::$completed,
                    self::$canceled,
                ];
            }
        };
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn(int $cents): float => $cents / 100,
            set: fn($dollars) => (int) round((((float)$dollars) * 100)),
        );
    }
}
