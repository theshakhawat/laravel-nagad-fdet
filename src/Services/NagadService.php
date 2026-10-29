<?php

namespace Theshakhawat\NagadFdet\Services;

use Illuminate\Support\Facades\Http;

class NagadService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $baseUrl = rtrim((string) config('nagad.base_url', 'https://nagad.banglaqr.online'), '/');
        if (str_ends_with($baseUrl, '/api')) {
            $baseUrl = substr($baseUrl, 0, -4);
        }

        $this->baseUrl = $baseUrl;
        $this->apiKey  = (string) config('nagad.api_key', '');
    }

    /**
     * Initiate checkout transaction and obtain Nagad payment URL.
     *
     * @param float $amount Minimum 1.00 BDT
     * @param string $orderId Unique order ID (Max 20 chars, no dashes recommended)
     * @param array $additionalMerchantInfo Optional metadata (ProductName, ProductSKU, etc.)
     * @return array
     */
    public function initiatePayment(float $amount, string $orderId, array $additionalMerchantInfo = []): array
    {
        if ($amount < 1) {
            throw new \InvalidArgumentException("Amount must be at least 1.00 BDT.");
        }

        if (empty($orderId)) {
            throw new \InvalidArgumentException("Order ID cannot be empty.");
        }

        if (strlen($orderId) > 20) {
            throw new \InvalidArgumentException("Order ID cannot exceed 20 characters.");
        }

        if (str_contains($orderId, '-')) {
            throw new \InvalidArgumentException("Order ID cannot contain a dash.");
        }

        $payload = [
            'amount'  => $amount,
            'orderId' => $orderId,
        ];

        if (!empty($additionalMerchantInfo)) {
            $payload['additionalMerchantInfo'] = $additionalMerchantInfo;
        }

        return $this->sendInitiatePayment($payload);
    }

    private function sendInitiatePayment(array $payload): array
    {
        $initiatePaymentUrl = "{$this->baseUrl}/api/nagad/payment/initiate";

        $response = Http::withHeaders([
            'API_KEY'      => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post($initiatePaymentUrl, $payload);

        return $response->json() ?? [
            'status'  => 'error',
            'code'    => $response->status(),
            'message' => $response->body() ?: 'Failed to connect to Nagad gateway.',
        ];
    }

    /**
     * Query authoritative verification status using payment reference ID.
     *
     * @param string $paymentReferenceId Base64 reference string
     * @return array
     */
    public function verifyPayment(string $paymentReferenceId): array
    {
        if (empty($paymentReferenceId)) {
            throw new \InvalidArgumentException("Payment reference ID cannot be empty.");
        }

        $verifyUrl = "{$this->baseUrl}/api/nagad/payment/verify";

        $response = Http::withHeaders([
            'API_KEY'      => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->get($verifyUrl, [
            'payment_ref_id' => $paymentReferenceId,
        ]);

        return $response->json() ?? [
            'status'  => 'error',
            'code'    => $response->status(),
            'message' => $response->body() ?: 'Failed to verify payment.',
        ];
    }

    /**
     * Check whether verification or callback result is successful.
     *
     * @param array $response
     * @return bool
     */
    public function isSuccess(array $response): bool
    {
        $statusCode = (string) ($response['statusCode'] ?? $response['status_code'] ?? $response['code'] ?? '');
        $status = strtolower((string) ($response['status'] ?? ''));

        return ($statusCode === '000' || $statusCode === '2000') || $status === 'success';
    }
}
