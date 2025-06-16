<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\RemoveFromOrderRequest;
use App\Http\Requests\Order\UpdateOrderQuantityRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Currency;
use App\Models\Order;
use App\Models\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{

    public function getCurrencies()
    {
        $currencies = Currency::all();
        return success(['currencies' => $currencies]);
    }


    public function showOrder(Order $order)
    {
        return success(['order' => $order]);
    }





    public function viewOrders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['products'])
            ->get();

        return success([
            'orders' => OrderResource::collection($orders)
        ], 200, 'Your orders.');
    }






    public function createOrder(Request $request)
    {
        $locale = app()->getLocale();
        $user = $request->user();
        $cart = $user->cart;
        $cart->load('products');

        $totalPrice = 0;
        $cartProducts = $cart->products;

        if ($cartProducts->isEmpty()) {
            return error('Your cart is empty', 422);
        }
        foreach ($cartProducts as  $product) {
            $quantity = $product->pivot->quantity;
            $totalPrice += $quantity * $product->price;
        }

        $storeId = $cart->products->first()->store_id;
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => round($totalPrice, 2),
            'store_id' => $storeId
        ]);

        foreach ($cartProducts as $product) {
            $quantity = $product->pivot->quantity;

            if ($quantity > $product->quantity) {
                throw new \Exception("Not enough stock for product: {$product->getName($locale)}");
            }

            $order->products()->attach($product->id, [
                'quantity' => $quantity,
                'price' => $product->price
            ]);
            $product->update(['quantity' => $product->quantity - $quantity]);
        }

        $cart->products()->detach();

        $paymentRequest = PaymentRequest::create([
            'user_id' => $user->id,
            'payable_id' => $order->id,
            'payable_type' => Order::class,
            'title' => [
                'en' => "Invoice for Order #{$order->id}",
                'ar' => "فاتورة للطلب رقم #{$order->id}",
            ],
            'description' => [
                'en' => "Payment due for Order #{$order->id} totaling \${$totalPrice} for user {$user->id} Mr. {$user->first_name} {$user->last_name} at " . Carbon::now() . ".",
                'ar' => "المبلغ المستحق للطلب رقم #{$order->id} بمجموع \${$totalPrice} للمستخدم السيد {$user->first_name} {$user->last_name} (معرف المستخدم: {$user->id}) بتاريخ " . Carbon::now() . ".",
            ],
            'price' => round($totalPrice, 2),
            'currency' => config('app.currency', 'USD'),
            'status' => PaymentRequest::status()->pending
        ]);

        return success([
            'order_id' => $order->fresh()->id,
            'payment_requset' => $paymentRequest,
        ], 201, 'Order created successfully');
    }







    public function UpdateOrderQuantity(UpdateOrderQuantityRequest $request, Order $order)
    {
        $locale = app()->getLocale();
        $data = $request->validated();
        $user = $request->user();

        $order->load('products');
        $product = $order->products()->firstWhere('product_id', $data['product_id']);

        $newQty = $data['quantity'];
        $oldQty = $product->pivot->quantity;
        $diff = $newQty - $oldQty;

        if ($diff >= 0) {
            if ($product->quantity < $diff) {
                return response()->json(['message' => "Not enough stock for product: {$product->getName($locale)}"], 400);
            }
            $product->decrement('quantity', $diff);
        } elseif ($diff < 0) {
            $product->increment('quantity', abs($diff));
        }

        $order->products()->updateExistingPivot($data['product_id'], [
            'quantity' => $newQty
        ]);

        $order->refresh();

        $totalPrice = $order->products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
        });

        $order->update(['total' => round($totalPrice, 2)]);

        $orderRequest = $order->paymentRequest;
        $orderRequest->update([
            'title' => [
                'en' => "Invoice for Order #{$order->id}",
                'ar' => "فاتورة للطلب رقم #{$order->id}",
            ],
            'description' => [
                'en' => "Payment due for Order #{$order->id} totaling \${$totalPrice} for user {$user->id} Mr. {$user->first_name} {$user->last_name} at " . Carbon::now() . ".",
                'ar' => "المبلغ المستحق للطلب رقم #{$order->id} بمجموع \${$totalPrice} للمستخدم السيد {$user->first_name} {$user->last_name} (معرف المستخدم: {$user->id}) بتاريخ " . Carbon::now() . ".",
            ],
            'price' => round($totalPrice, 2)
        ]);

        return success([
            'order' => $order,
            'order_products' => $order->products,
            'payment_request' => $orderRequest
        ], 200, 'Order updated successfully');
    }



    public function removeProduct(RemoveFromOrderRequest $request, Order $order)
    {
        $data = $request->validated();
        $order->load('products');

        $product = $order->products()->firstWhere('product_id', $data['product_id']);

        $product->increment('quantity', $product->pivot->quantity);

        $order->products()->detach($data['product_id']);
        $order->refresh();

        if ($order->products->isEmpty()) {
            $order->paymentRequest()->delete();
            $order->delete();
            return success([], 200, 'Order deleted successfully.');
        }

        $totalPrice = $order->products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
        });

        $order->update([
            'total' => round($totalPrice, 2)
        ]);

        $orderRequest = $order->paymentRequest;
        $orderRequest->update([
            'title' => [
                'en' => "Invoice for Order #{$order->id}",
                'ar' => "فاتورة للطلب رقم #{$order->id}",
            ],
            'description' => [
                'en' => "Payment due for Order #{$order->id} totaling \${$totalPrice}",
                'ar' => "المبلغ المستحق للطلب رقم #{$order->id} بمجموع \${$totalPrice}",
            ],
            'price' => round($totalPrice, 2)
        ]);

        return success([
            'order' => $order,
            'order_products' => $order->products,
            'payment_request' => $orderRequest
        ], 200, 'product removed successfully');
    }



    public function cancelOrder(Order $order)
    {
        Gate::authorize('cancelOrder', $order);
        $order->load('products');

        foreach ($order->products as $product) {
            $product->increment('quantity', $product->pivot->quantity);
        }

        $order->products()->detach();
        $order->paymentRequest()->delete();
        $order->update(['status' => 'canceled']);

        return response()->json([
            'message' => 'Order canceled successfully.'
        ], 200);
    }
}
