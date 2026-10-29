# Nagad Payment Gateway (Freelancer Digital Expert Team)

Laravel package for integrating the Nagad Modified Payment Gateway (via https://nagad.banglaqr.online).

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require theshakhawat/laravel-nagad-fdet
```

The package supports **Laravel Auto-Discovery**. The ServiceProvider and Facade (`NagadFdet`) will be registered automatically.

---

## ⚙️ Configuration & Environment

Publish the configuration file:

```bash
php artisan vendor:publish --tag=nagad-config
```

Add your credentials to your `.env` file:

```env
NAGAD_API_KEY="np_live_your_48_character_api_key_here"
NAGAD_BASE_URL="https://nagad.banglaqr.online"
```

> **Note:** Make sure you set your website Callback URL in the Nagad Merchant Portal (e.g., `https://yourdomain.com/nagad/payment/callback`).

---

## 🛠️ Usage

### 1. Initiate Payment

```php
use Theshakhawat\NagadFdet\Facades\NagadFdet;

// Amount must be >= 1.00 BDT
$amount = 1000.00;

// Order ID must be max 20 characters (no dashes)
$orderId = 'ORD' . time();

// Optional custom metadata
$additionalInfo = [
    'ProductName'   => 'Premium Cotton T-Shirt',
    'ProductSKU'    => 'TSH-BLK-001',
    'CustomerPhone' => '017XXXXXXXX',
];

$response = NagadFdet::initiatePayment($amount, $orderId, $additionalInfo);

if (!empty($response['paymentRedirectUrl'])) {
    // Redirect customer to Nagad secure checkout page
    return redirect()->away($response['paymentRedirectUrl']);
}

// Handle error
return back()->with('error', $response['message'] ?? 'Payment initiation failed.');
```

---

### 2. Handle Callback & Verify

In your controller handling the callback:

```php
use Illuminate\Http\Request;
use Theshakhawat\NagadFdet\Facades\NagadFdet;

public function handleCallback(Request $request)
{
    $paymentRefId = $request->query('payment_ref_id');

    if (!$paymentRefId) {
        return redirect()->route('payment.failed')->with('error', 'Payment Reference ID missing.');
    }

    // Query authoritative verification status from Nagad
    $verification = NagadFdet::verifyPayment($paymentRefId);

    if (NagadFdet::isSuccess($verification)) {
        // Payment successful!
        // Update order status in your database
        $orderId = $verification['orderId'] ?? $request->query('order_id');
        $amount = $verification['amount'] ?? null;

        return redirect()->route('payment.success')->with('message', 'Payment successful!');
    }

    // Payment failed or cancelled
    return redirect()->route('payment.failed')->with('error', 'Payment failed or cancelled.');
}
```

---

### 3. Verify Payment Directly

```php
$verification = NagadFdet::verifyPayment('PAYMENT_REF_ID_HERE');

if (NagadFdet::isSuccess($verification)) {
    // Success (code 000)
}
```

---

## 📌 Gateway Error & Status Codes Reference

| Code | Status Message | Meaning |
| :--- | :--- | :--- |
| `2000` / `000` | Payment URL generated / Success | Payment initiated or completed successfully. |
| `9999` | Payment Aborted / Cancelled | Customer cancelled or timed out. |
| `50_0026_051` | Payment Failed / OTP Error | Invalid PIN/OTP or insufficient balance. |
| `1001` | API Key not found! | Header `API_KEY` was missing. |
| `1002` | Invalid API Key | API Key does not match any registered website. |
| `1003` | Your Website is inactive | Website status toggled inactive in Admin Panel. |
| `1004` | Data missing or data validation failed | Amount < 1 or orderId > 20 characters. |
| `1008` | Payment Reference ID not found! | Invalid paymentRefId passed to verify endpoint. |

---

## 📄 License
MIT License.

