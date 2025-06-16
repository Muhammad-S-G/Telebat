<?php

namespace App\Services\Payment\PayPal;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\ShipOrderNotification;
use App\Services\Payment\PaymentService;
use Exception;
use Illuminate\Support\Facades\DB;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

use function Laravel\Prompts\error;

class PayPalService extends PaymentService
{
    private PayPalHttpClient $client;
    public function __construct()
    {
        $environment = config('services.paypal.mode') === 'live'
            ? new ProductionEnvironment(config('services.paypal.client_id'), config('services.paypal.client_secret'))
            : new SandboxEnvironment(config('services.paypal.client_id'), config('services.paypal.client_secret'));

        $this->client = new PayPalHttpClient($environment);
    }

    public function createOrder(float $total, string $currency, string $description)
    {
        if ($total <= 0) {
            throw new \Exception(__("messages.the_total_price_must_be_greater_than_zero"), 400);
        }

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $total
                    ],
                    'description' => $description
                ],
            ],
            'application_context' => [
                'return_url' => route('paypal.sucess'),
                'cancel_url' => route('paypal.cancel'),
            ],
        ];

        $response = $this->client->execute($request);
        $approvalUrl = null;

        foreach ($response->result->links as $link) {
            if ($link->rel === 'approve') {
                $approvalUrl = $link->href;
                break;
            }
        }

        if (!$approvalUrl) {
            throw new \Exception('Unable to generate PayPal approvl URL');
        }

        return [
            'paypal_order_id' => $response->result->id,
            'approval_url' => $approvalUrl
        ];
    }

    public function captureOrder(string $orderId)
    {
        try {

            $orderDetails = $this->getOrderDetails($orderId);

            $amountPaid = $orderDetails->purchase_units[0]->amount->value;

            $payment = Payment::where('paypal_order_id', $orderId)->firstOrFail();

            $paymentRequest = $payment->paymentRequest;
            $total = $paymentRequest->price;
            $totalInCents = (int)round((((float)$total) * 100));

            return DB::transaction(function () use ($payment, $paymentRequest, $orderDetails, $amountPaid, $totalInCents, $orderId) {

                $amountPaidInCents = (int)round(($amountPaid) * 100);

                if ((string)$totalInCents !== (string)$amountPaidInCents) {
                    return error(
                        __("messages.total_not_equal_paid"),
                        [
                            __("messages.total_not_equal_paid")
                        ],
                        400
                    );
                }

                $request = new OrdersCaptureRequest($orderId);
                $request->prefer('return=representation');
                $response = $this->client->execute($request);

                if ($response->result->status === 'COMPLETED') {
                    $paymentRequest->update(['status' => PaymentRequest::status()->completed]);
                    $captureDetails = $response->result->purchase_units[0]->payments->captures[0] ?? null;

                    $payment->update([
                        'payment_id' => $captureDetails->id ?? null,
                        'paypal_payer_email' => $response->result->payer->email_address,
                        'paypal_payer_id' => $response->result->payer->payer_id,
                        'currency' => $orderDetails->purchase_units[0]->amount->currency_code,
                        'status' => $response->result->status,
                        'description' => $response->result->purchase_units[0]->description ?? null,
                        'response' => json_encode($response),
                        'completed_at' => now()->toDateString()
                    ]);

                    if ($paymentRequest->payable_type === Order::class) {
                        $order = Order::findOrFail($paymentRequest->payable_id);
                        $order->update(['status' => 'approved']);
                        $user = $order->user;
                        $vendor = $order->store()->first()->vendor;
                        if ($user) {
                            $user->notify(new OrderStatusUpdated($order, $order->status));
                        }
                        if ($vendor) {
                            $vendor->notify(new ShipOrderNotification($order));
                        }
                    }
                    return success([
                        'data' => $paymentRequest
                    ]);
                }

                if ($response->result->status !== 'COMPLETED') {
                    return error(__('messages.payment_not_approved'), [
                        __('messages.payment_not_approved')
                    ], 400);
                }


                throw new \Exception(__("messages.payment_not_approved"));
                return error(__('messages.payment_not_approved'), [
                    __('messages.payment_not_approved')
                ], 400);
            });
        } catch (\Exception $e) {
            return error($e->getMessage(), [
                $e->getMessage(),
            ], 500);
        }
    }




    public function getOrderDetails(string $orderId)
    {
        try {
            $request = new OrdersGetRequest($orderId);
            $response = $this->client->execute($request);
            return $response->result;
        } catch (\Exception $e) {
            throw new \Exception('Unable to fetch PayPal order details' . $e->getMessage());
        }
    }
}
