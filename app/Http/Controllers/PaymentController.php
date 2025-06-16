<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\GetInvoiceRequest;
use App\Models\Payment;
use App\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function showInvoice(Payment $payment, GetInvoiceRequest $request)
    {
        $download = $request->boolean('download', false);
        return $this->paymentService->getInvoice($payment, $download);
    }
}
