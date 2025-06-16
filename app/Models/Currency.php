<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'precision',
        'active'
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
