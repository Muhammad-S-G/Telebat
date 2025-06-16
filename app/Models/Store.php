<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'vendor_id',
        'name',
        'longitude',
        'latitude',
        'image'
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public function getName($lang = 'en')
    {
        return $this->name[$lang]
            ?? $this->name[config('app.fallback_locale')]
            ?? null;
    }


    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'store_id');
    }
}
