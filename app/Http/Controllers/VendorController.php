<?php

namespace App\Http\Controllers;

use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Vendor\VendorStoresResource;
use App\Models\Order;
use App\Models\Store;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VendorController extends Controller
{

    public function myStores(Request $request)
    {
        $user = $request->user();
        $stores = Store::where('vendor_id', $user->id)->get();
        return VendorStoresResource::collection($stores);
    }

    public function getMyStoreOrders(Request $request, Store $store)
    {
        $user = $request->user();
        Gate::denyIf($store->vendor_id !== $user->id);
        $user = $request->user();
        $orders = $store->orders()->whereIn('status', ['approved', 'delivering'])
            ->with(['products' => function ($query) use ($user) {
                $query->withCount([
                    'favoriteBy as is_favorite' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
                ]);
            }])->get();

        return success([
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function deliverOrder(Request $request, Order $order)
    {
        $user = $request->user();
        Gate::denyIf($order->store()->first()->vendor_id !== $user->id);
        if ($order->status != 'approved') {
            return response()->json([
                'success' => false,
                'message' => "Order status is {$order->status}. Only approved orders can be marked as delivering."
            ]);
            return error("Order status is {$order->status}. Only approved orders can be marked as delivering.");
        }

        $order->update([
            'status' => 'delivering'
        ]);

        $user = $order->user;
        $user->notify(new OrderStatusUpdated($order, $order->status));

        return success([
            'order' => $order->fresh()
        ], 200, 'Order status changed to delivering.');
    }


    public function completedOrder(Request $request, Order $order)
    {
        $user = $request->user();
        Gate::denyIf($order->store()->first()->vendor_id !== $user->id);

        if ($order->status != 'delivering') {
            return error("Order status is {$order->status}. Only delivering orders can be marked as completed.");
        }

        $order->update([
            'status' => 'delivered'
        ]);

        $user = $order->user;
        $user->notify(new OrderStatusUpdated($order, $order->status));

        return success([
            'order' => $order->fresh(),
        ], 200, 'Order status changed to delivered.');
    }
}
