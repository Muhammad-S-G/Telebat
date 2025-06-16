<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'section_id',
        'name',
        'description',
        'price',
        'quantity',
        'image'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];


    public function scopeSearch(Builder $query, ?string $term, string $locale): Builder
    {
        if (!$term) {
            return $query;
        }

        $jsonPath = '$.' . $locale;

        return $query->where(function (Builder $q) use ($term, $jsonPath) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, ?)) LIKE ?", [$jsonPath, "%{$term}%"])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, ?)) LIKE ?", [$jsonPath, "%{$term}%"]);
        });
    }


    public function scopeFilterByStore(Builder $query, ?int $StoreId): Builder
    {
        return $StoreId
            ? $query->where('store_id', $StoreId)
            : $query;
    }

    public function scopeFilterBySection(Builder $query, ?int $sectionId): Builder
    {
        return $sectionId
            ? $query->where('section_id', $sectionId)
            : $query;
    }

    public function scopeFilterByPrice(Builder $query, ?array $range): Builder
    {
        return $range
            ? $query->whereBetween('price', $range)
            : $query;
    }

    public function scopeFilterByquantity(Builder $query, ?array $range): Builder
    {
        return $range
            ? $query->whereBetween('quantity', $range)
            : $query;
    }

    public function scopeSort(Builder $query, ?string $field, ?string $direction = 'asc', string $locale = 'en'): Builder
    {
        if (in_array($field, ['price', 'quantity'], true)) {
            return $query->orderBy($field, $direction);
        }

        if ($field === 'name') {
            return $query->orderBy("name->{$locale}", $direction);
        }
        return $query;
    }



    public function getName($lang)
    {
        return $this->name[$lang] ?? null;
    }

    public function getDescription($lang)
    {
        return $this->description[$lang] ?? null;
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function carts()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function favoriteBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
