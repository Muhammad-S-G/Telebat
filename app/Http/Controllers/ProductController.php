<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Storage;

use function Pest\Laravel\json;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        $locale = app()->getLocale();
        $perPage = config('pagination.per_page');

        $search = $request->input('search');
        $storeId = $request->input('store_id');
        $sectionId = $request->input('section_id');
        $priceRange = $request->input('price_range');
        $quantityRange = $request->input('quantity_range');
        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_dir');

        $products = Product::withCount([
            'favoriteBy as is_favorite' => fn($q) => $q->where('user_id', $user->id)
        ])
            ->search($search, $locale)
            ->filterBySection($sectionId)
            ->filterByStore($storeId)
            ->filterByPrice($priceRange)
            ->filterByquantity($quantityRange)
            ->sort($sortField, $sortDirection)
            ->paginate($perPage);

        return new ProductCollection($products);
    }




    public function store(StoreProductRequest $request)
    {
        $user_id = $request->user()->id;
        $validated = $request->validated();
        $store = Store::findOrFail($validated['store_id']);
        Gate::denyIf($user_id !== $store->vendor_id);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        } else {
            $imagePath = null;
        }

        $product = Product::create([
            'section_id' => $store->section_id,
            'store_id' => $request->store_id,
            'name' => ['ar' => $validated['ar_name'], 'en' => $validated['en_name']],
            'description' => ['ar' => $validated['ar_description'], 'en' => $validated['en_description']],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'image' => $imagePath,
        ]);

        $locale = app()->getLocale();
        $newProduct = [
            'section_id' => $product->section_id,
            'store_id' => $product->store_id,
            'name' => $product->getName($locale),
            'description' => $product->getDescription($locale),
            'price' => $product->price,
            'quantity' => $product->quantity,
            'image' => $imagePath,
        ];

        return success(['product' => $newProduct], 201);
    }



    public function update(UpdateProductRequest $request, Product $product)
    {
        $user_id = $request->user()->id;
        $product->load('favoriteBy');
        $is_favorite = $product->favoriteBy()->wherePivot('user_id', $user_id)->exists();
        $data = $request->safe()->only(['ar_name', 'en_name', 'ar_description', 'en_description', 'price', 'quantity']);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        if ($request->filled(['ar_name', 'en_name'])) {
            $existing = array_merge(['ar' => null, 'en' => null], $product->name);
            $override = array_filter([
                'ar' => $data['ar_name'] ?? null,
                'en' => $data['en_name'] ?? null,
            ]);
            $product->name = array_merge($existing, $override);
        }

        if ($request->filled(['ar_description', 'en_description'])) {
            $existing = array_merge(['ar' => null, 'en' => null], $product->description);
            $override = array_filter([
                'ar' => $data['ar_description'] ?? null,
                'en' => $data['en_description'] ?? null,
            ]);
            $product->description = array_merge($existing, $override);
        }

        if ($request->filled('price')) {
            $product->price = $data['price'];
        }

        if ($request->filled('quantity')) {
            $product->quantity = $data['quantity'];
        }

        if (!$product->isDirty() && !$request->hasFile('image')) {
            return success([
                'product' => $product,
                'is_favorite' => $is_favorite
            ], 200, 'Nothing to update');
        }

        $product->save();

        return success([
            'product' => $product->fresh(),
            'is_favorite' => $is_favorite
        ], 200, 'Product updated successfully.');
    }





    public function show(Request $request, Product $product)
    {
        $user_id = $request->user()->id;
        $is_favorite = $product->favoriteBy()->wherePivot('user_id', $user_id)->exists();
        return success(['Product' => $product, 'is_favorite' => $is_favorite]);
    }




    public function destroy(Request $request, Product $product)
    {
        $user_id = $request->user()->id;
        $storeVendor = $product->store->vendor_id;
        Gate::denyIf($user_id !== $storeVendor, 'unauthorized action');

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return success([], 200, 'Product deleted successfully.');
    }



    public function getFavorites(Request $request)
    {
        $locale = app()->getLocale();
        $user = $request->user();
        $favoriteProducts = $user->favoriteProducts()->get();

        $localizedFavorites = $favoriteProducts->map(function ($product) use ($locale) {
            return [
                'id' => $product->id,
                'store_id' => $product->store_id,
                'section_id' => $product->section_id,
                'name' => $product->getName($locale),
                'description' => $product->getDescription($locale),
                'price' => $product->price,
                'quantity' => $product->quantity,
                'image' => $product->image,
            ];
        });

        return success([
            'favorite products' => $localizedFavorites
        ], 200);
    }



    public function addToFavorites(Request $request, Product $product)
    {
        $user = $request->user();
        $user->favoriteProducts()->syncWithoutDetaching($product);

        return success([
            'product' => $product
        ], 200, 'Product added to your favorites');
    }


    public function removeFromFavorites(Request $request, Product $product)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);
        return success([], 200, 'Product removed from your favorites');
    }
}
