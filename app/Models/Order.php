<?php

namespace App\Models;

use App\Traits\CreatedAtDescScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;



class Order extends Model
{
    use HasFactory, CreatedAtDescScopeTrait;

    protected $fillable = [
        'user_id',
        'status',
        'total',
        'store_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    public function paymentRequest(): MorphOne
    {
        return $this->morphOne(PaymentRequest::class, 'payable');
    }


    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
