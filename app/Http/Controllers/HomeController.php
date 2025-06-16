<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use App\Models\Store;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $user = $request->user();

        $latestProducts = Product::withCount([
            'favoriteBy as is_favorite' => fn($q) => $q->where('user_id', $user->id)
        ])->latest()->take(10)->get()
            ->map(function ($product) use ($locale) {
                return  [
                    'id' => $product->id,
                    'name' => $product->getName($locale),
                    'description' => $product->getDescription($locale),
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'image' => $product->image,
                    'store_id' => $product->store_id,
                    'section_id' => $product->section_id,
                    'is_favorite' => (bool)$product->is_favorite,
                ];
            });


        $sections = Section::latest()->take(10)->get()
            ->map(function ($section) use ($locale, $user) {
                return [
                    'id' => $section->id,
                    'name' => $section->getName($locale),
                    'description' => $section->getDescription($locale),
                    'image' => $section->image,
                ];
            });

        $latestStores = Store::latest()->take(10)
            ->get()
            ->map(function ($store) use ($locale) {
                return [
                    'id' => $store->id,
                    'name' => $store->getName($locale),
                    'image' => $store->image,
                ];
            });

        return success([
            'latest_products' => $latestProducts,
            'sections' => $sections,
            'latest_stores' => $latestStores
        ]);
    }
}
