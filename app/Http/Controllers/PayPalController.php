<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\PayPalCheckoutRequest;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Services\Payment\PayPal\PayPalService;
use Illuminate\Http\Request;
use function Laravel\Prompts\error;

class PayPalController extends Controller
{

    public function __construct(protected PayPalService $payPalService) {}

    public function checkout(PayPalCheckoutRequest $request)
    {

        $validated = $request->validated();
        $user = $request->user();
        $paymentRequest = PaymentRequest::findOrFail($validated['payment_request_id']);
        $currency = Currency::findOrFail($validated['currency_id']);

        $total = $paymentRequest->price;
        $description = $paymentRequest->description;

        $paypalOrderData = $this->payPalService->createOrder($total, $currency->code, $description);

        $payment = $user->payments()->create([
            'model_type' => PaymentRequest::class,
            'model_id' => $paymentRequest->id,
            'payment_request_id' => $paymentRequest->id,
            'payment_method' => Payment::methods()->paypal,
            'status' => Payment::status()->pending,
            'paypal_order_id' => $paypalOrderData['paypal_order_id'],
            'price' => $paymentRequest->price,
            'currency' => strtolower($currency->code)
        ]);

        return success([
            'approval_url' => $paypalOrderData['approval_url'],
            'paypal_order_id' => $paypalOrderData['paypal_order_id']
        ]);
    }


    public function handleSuccess(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        try {
            return $this->payPalService->captureOrder($request->input('token'));
        } catch (\Exception $e) {
            return error(__("messages.payment_not_approved"), ['message' => __("messages.payment_not_approved")], 400);
        }
    }




    public function handleCancel(Request $request)
    {
        $orderId = $request->query('token');

        if ($orderId) {
            $payment = Payment::where('paypal_order_id', $orderId)
                ->where('status', Payment::status()->pending)
                ->first();
            if ($payment) {
                $pr = $payment->paymentRequest;
                $payment->update(['status' => Payment::status()->canceled]);
                $pr->update(['status' => PaymentRequest::status()->canceled]);

                if ($pr->payable_type === Order::class) {
                    Order::find($pr->payable_id)->update(['status' => 'canceled']);
                }
            }
        }

        return response()->json([
            'status'  => 'cancelled',
            'message' => __('messages.payment_cancelled'),
        ], 200);
    }
}
