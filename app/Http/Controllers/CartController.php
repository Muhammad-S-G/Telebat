<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartRequest;
use App\Http\Requests\Cart\removeCartRequest;
use App\Http\Resources\Cart\CartProductResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CartController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        $locale = app()->getLocale();
        $per_page = config('pagination.per_page');

        $cart = $user->cart()->firstOrCreate();
        $cartProducts = $cart->products()->paginate($per_page);

        if ($cartProducts->isEmpty()) {
            return success([], 200, 'Your cart is empty');
        }

        return success([
            'cart products' => CartProductResource::collection($cartProducts)
        ], 200, 'The products in your cart.');
    }




    public function addToCart(CartRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();
        $cart = $user->cart()->with('products')->firstOrCreate();

        $product = Product::findOrFail($validated['product_id']);

        $firstItem  = $cart->products->first(); // Getting the store_id of the first product added to the cart or if it is empty add whatever
        $storeId = $firstItem  ? $firstItem->store_id : $product->store_id; // To make sure only products from the same store are added to the cart

        if ($storeId !== $product->store_id) {
            return error(['message' => 'All items in a cart must belong to the same store.'], 400, [
                'existing_cart_store_id' => $storeId,
                'attempted_product_store_id' => $product->store_id,
            ]);
        }

        $existsInCart = $cart->products()->where('product_id', $product->id)->first();

        if ($existsInCart) {
            $newQuantity = $existsInCart->pivot->quantity + $validated['quantity'];
            $cart->products()->updateExistingPivot($product->id, ['quantity' => $newQuantity]);
        } else {
            $cart->products()->attach($product->id, ['quantity' => $validated['quantity']]);
        }

        return success([
            'product' => $product
        ], 200, 'Product added to your cart');
    }





    public function updateCartItem(CartRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();
        $cart = $user->cart()->firstOrFail();
        $cartProduct = $cart->products()->where('product_id', $validated['product_id'])->first();

        if (!$cartProduct) {
            return error('Product not found in cart !!', 404);
        }

        $newQuantity = $validated['quantity'];
        $cart->products()->updateExistingPivot($cartProduct->id, ['quantity' => $newQuantity]);
        return success([], 200, 'Yor cart product updated successfully');
    }





    public function removeFromCart(removeCartRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();
        $cart = $user->cart()->firstOrFail();
        $product = $cart->products()->where('product_id', $validated['product_id'])->first();

        if (!$product) {
            return error('Product not found in your cart !!', 404);
        }

        $cart->products()->detach($product->id);

        return success([
            'product' => $product
        ], 200, 'Product removed successfully');
    }

    public function clearCart(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart()->firstOrFail();
        $cart->products()->detach();
        return success([], 200, 'Cart cleared successfully !!');
    }
}
