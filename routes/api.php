<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;


Route::prefix('sections')->middleware(['auth:sanctum', 'is_verified'])->group(
    function () {
        Route::get('/', [SectionController::class, 'index'])->name('sections.index');

        Route::post('/', [SectionController::class, 'store'])->middleware('role:admin');

        Route::get('/{section}', [SectionController::class, 'show']);

        Route::patch('/{section}', [SectionController::class, 'update'])->middleware('role:admin');

        Route::delete('/{section}', [SectionController::class, 'destroy'])->middleware('role:admin');
    }

);



Route::prefix('stores')->middleware(['auth:sanctum', 'is_verified'])->group(
    function () {
        Route::get('/', [StoreController::class, 'index'])->name('stores.index');

        Route::post('/', [StoreController::class, 'store'])->middleware('role:vendor');

        Route::get('/{store}', [StoreController::class, 'show']);

        Route::post('/{store}', [StoreController::class, 'update'])->middleware('role:vendor');

        Route::delete('/{store}', [StoreController::class, 'destroy'])->middleware('role:vendor');
    }
);




Route::prefix('products')->middleware(['auth:sanctum', 'is_verified'])->group(
    function () {
        Route::get('/', [ProductController::class, 'index']);

        Route::post('/', [ProductController::class, 'store'])->middleware('role:vendor');

        Route::get('/{product}', [ProductController::class, 'show']);

        Route::patch('/{product}', [ProductController::class, 'update'])->middleware('role:vendor');

        Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('role:vendor');
    }
);



Route::prefix('favorites')->middleware(['auth:sanctum', 'is_verified'])->group(
    function () {
        Route::get('/', [ProductController::class, 'getFavorites'])->middleware('role:user');

        Route::post('/{product}', [ProductController::class, 'addToFavorites'])->middleware('role:user');

        Route::delete('/{product}', [ProductController::class, 'removeFromFavorites'])->middleware('role:user');
    }
);






Route::prefix('cart')->middleware(['auth:sanctum', 'is_verified', 'role:user'])->group(
    function () {
        Route::get('/', [CartController::class, 'index']);

        Route::post('/', [CartController::class, 'addToCart']);

        Route::put('/', [CartController::class, 'updateCartItem']);

        Route::delete('/remove', [CartController::class, 'removeFromCart']);

        Route::delete('/clear', [CartController::class, 'clearCart']);
    }
);



Route::middleware(['auth:sanctum', 'is_verified', 'role:user'])->prefix('orders')->group(
    function () {
        Route::get('/currencies', [OrderController::class, 'getCurrencies'])->name('currencies.list');

        Route::get('/{order}', [OrderController::class, 'showOrder'])->name('show.order');

        Route::get('/', [OrderController::class, 'viewOrders'])->name('list.orders');

        Route::post('/', [OrderController::class, 'createOrder'])->name('create.order');

        Route::put('/{order}', [OrderController::class, 'updateOrderQuantity'])->name('update.order');

        Route::delete('/{order}', [OrderController::class, 'removeProduct'])->name('remove.product');

        Route::delete('/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('cancel.order');
    }
);


Route::middleware(['auth:sanctum', 'is_verified', 'role:user'])->prefix('payments')->group(
    function () {
        Route::post('/{order}/stripe/payment-intent', [StripeController::class, 'createPaymentIntent'])
            ->middleware('is_json')->name('stripe.createIntent');

        Route::get('/{payment}/invoice', [PaymentController::class, 'showInvoice']);

        Route::post('paypal/checkout', [PayPalController::class, 'checkout'])
            ->middleware('is_json')->name('paypal.create');
    }
);

Route::post('webhook/stripe', [StripeController::class, 'handleWebhook'])->name('stripe.webhook');
Route::get('paypal/success', [PayPalController::class, 'handleSuccess'])->name('paypal.sucess');
Route::get('paypal/cancel', [PayPalController::class, 'handleCancel'])->name('paypal.cancel');


Route::middleware(['auth:sanctum', 'is_verified', 'role:vendor'])->prefix('dashboard')->group(
    function () {

        Route::get('/stores', [VendorController::class, 'myStores'])->name('vendor.stores');

        Route::get('/{store}/orders', [VendorController::class, 'getMyStoreOrders'])->name('vendor.orders');

        Route::patch('/orders/{order}/delivering', [VendorController::class, 'deliverOrder'])->name('orders.deliver');

        Route::patch('/orders/{order}/delivered', [VendorController::class, 'completedOrder'])->name('mark.delivered');
    }
);


Route::middleware(['auth:sanctum', 'is_verified', 'role:user|vendor'])->prefix('notifications')->group(
    function () {
        Route::get('/', [NotificationController::class, 'index']);

        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead']);

        Route::get('/unread', [NotificationController::class, 'unread']);

        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
    }
);

Route::get('/home', [HomeController::class, 'index'])->middleware(['auth:sanctum', 'is_verified', 'role:user']);





require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
