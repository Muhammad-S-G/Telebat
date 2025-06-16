<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    public function getName($lang)
    {
        return $this->name[$lang] ?? null;
    }

    public function getDescription($lang)
    {
        return $this->description[$lang] ?? null;
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
