<?php

namespace Theshakhawat\NagadFdet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Theshakhawat\NagadFdet\Facades\NagadFdet;

class NagadPaymentController extends Controller
{
    /**
     * Display demo checkout page.
     */
    public function checkout()
    {
        if (view()->exists('nagad.pay')) {
            return view('nagad.pay');
        }

        return view('nagad::pay');
    }

    /**
     * Initiate checkout transaction.
     */
    public function initiatePayment(Request $request)
    {
        $amount = (float) $request->input('amount', 100);
        $orderID = (string) $request->input('order_id', 'ORD' . time());

        $additionalData = $request->input('additionalMerchantInfo', [
            'ProductName'   => $request->input('product_name', 'Demo Product'),
            'ProductSKU'    => $request->input('product_sku', 'SKU-001'),
            'CustomerPhone' => $request->input('customer_phone', '01700000000'),
        ]);

        try {
            $response = NagadFdet::initiatePayment($amount, $orderID, $additionalData);

            if (!empty($response['paymentRedirectUrl'])) {
                return redirect()->away($response['paymentRedirectUrl']);
            }

            return response()->json([
                'status'  => 'error',
                'message' => $response['message'] ?? 'Payment URL generation failed',
                'data'    => $response,
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Handle Nagad callback redirect.
     */
    public function handleCallback(Request $request)
    {
        $paymentRefId = $request->query('payment_ref_id') ?? $request->input('payment_ref_id');
        $status = $request->query('status');
        $statusCode = $request->query('status_code');
        $orderId = $request->query('order_id');

        if (!$paymentRefId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment reference ID (payment_ref_id) is missing in callback.',
                'data'    => $request->all(),
            ], 400);
        }

        // Authoritative verification via Nagad Verify API
        $verification = NagadFdet::verifyPayment($paymentRefId);
        $isSuccess = NagadFdet::isSuccess($verification);

        return response()->json([
            'status'         => $isSuccess ? 'success' : 'failed',
            'message'        => $isSuccess ? 'Payment completed successfully' : 'Payment was not completed',
            'is_paid'        => $isSuccess,
            'callback_data'  => $request->all(),
            'verification'   => $verification,
        ]);
    }

    /**
     * Directly query verification status.
     */
    public function verifyPayment(Request $request, ?string $paymentRefId = null)
    {
        $refId = $paymentRefId ?: $request->input('payment_ref_id');

        if (!$refId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment reference ID (payment_ref_id) is required.',
            ], 400);
        }

        $verification = NagadFdet::verifyPayment($refId);
        $isSuccess = NagadFdet::isSuccess($verification);

        return response()->json([
            'status'  => 'success',
            'is_paid' => $isSuccess,
            'data'    => $verification,
        ]);
    }
}
