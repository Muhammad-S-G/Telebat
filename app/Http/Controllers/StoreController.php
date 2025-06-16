<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Http\Resources\Store\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $search = $request->input('search', null);
        $jsonPath = '$.' . $locale;

        $stores = Store::query()
            ->when($search, function ($query) use ($jsonPath, $search) {
                $query->where(function ($q) use ($jsonPath, $search) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, ?)) LIKE ?", [$jsonPath, "%{$search}%"]);
                });
            })
            ->paginate(5);

        if ($stores->isEmpty()) {
            return success([
                'pagination' => [
                    'current_page' => $stores->currentPage(),
                    'last_page' => $stores->lastPage(),
                    'per_page' => $stores->perPage(),
                    'total' => $stores->total(),
                ]
            ], 200, 'No stores found for the given criteria.');
        }

        if ($stores->currentPage() > $stores->lastPage()) {
            return redirect()->route('stores.index', ['page' => 1]);
        }

        return StoreResource::collection($stores);
    }


    public function store(CreateStoreRequest $request)
    {
        $locale = app()->getLocale();
        $user = $request->user();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stores', 'public');
        } else {
            $imagePath = null;
        }

        $store = Store::create([
            'name' => ['ar' => $request->ar_name, 'en' => $request->en_name],
            'section_id' => $request->section_id,
            'vendor_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image' => $imagePath
        ]);


        $newStore = [
            'name' => $store->getName($locale),
            'section_id' => $request->section_id,
            'vendor_id' => $store->vendor_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image' => $imagePath,
        ];

        return success(['store' => $newStore], 201);
    }







    public function update(UpdateStoreRequest $request, Store $store)
    {

        $data = $request->safe()->only(['section_id', 'ar_name', 'en_name', 'latitude', 'longitude']);

        if ($request->hasFile('image')) {
            if ($store->image && Storage::disk('public')->exists($store->image)) {
                Storage::disk('public')->delete($store->image);
            }
            $store->image = $request->file('image')->store('stores', 'public');
        }

        if ($request->filled(['ar_name', 'en_name'])) {
            $existing = array_merge(['ar' => null, 'en' => null], $store->name);
            $override = array_filter([
                'ar' => $data['ar_name'] ?? null,
                'en' => $data['en_name'] ?? null
            ]);
            $store->name = array_merge($existing, $override);
        }

        if ($request->filled(['section_id'])) {
            $store->section_id = $data['section_id'];
        }
        if ($request->filled(['latitude'])) {
            $store->latitude = $data['latitude'];
        }
        if ($request->filled(['longitude'])) {
            $store->longitude = $data['longitude'];
        }


        if (!$store->isDirty() && !$request->hasFile('image')) {
            return success([
                'store' => $store
            ], 200, 'Nothing updated');
        }

        $store->save();
        return success([
            'store' => $store->fresh()
        ]);
    }



    public function show(Store $store)
    {
        return success(['section' => $store]);
    }




    public function destroy(Request $request, Store $store)
    {
        $user_id = $request->user()->id;
        Gate::denyIf($user_id !== $store->vendor_id, 'Unauthorized action');
        if (Storage::disk('public')->exists($store->image)) {
            Storage::disk('public')->delete($store->image);
        }
        $store->delete();
        return success([], 200, 'store deleted successfully');
    }
}
