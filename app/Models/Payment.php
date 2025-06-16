<?php

namespace App\Models;

use App\Traits\CreatedAtDescScopeTrait;
use App\Traits\DateFormatTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;




class Payment extends Model
{
    use HasFactory, SoftDeletes, DateFormatTrait, CreatedAtDescScopeTrait;

    protected $fillable = [
        'model_id',
        'model_type',

        'payment_request_id',
        'user_id',

        'payment_method',
        'status',

        'paypal_order_id',
        'paypal_payer_id',
        'paypal_payer_email',
        'paypal_capture_response',

        'stripe_client_secret',
        'stripe_webhook_payload',

        'response',
        'description',

        'price',
        'currency',

        'completed_at',
    ];

    public static function methods()
    {
        return new class {
            public $paypal = 'PAYPAL';
            public $stripe = 'STRIPE';
            public function all()
            {
                return [
                    self::$paypal,
                    self::$stripe
                ];
            }
        };
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

    public function model()
    {
        return $this->morphTo();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function paymentRequest()
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn(int $cents): float => $cents / 100,
            set: fn($dollars) => (int) round((((float)$dollars) * 100)),
        );
    }
}
