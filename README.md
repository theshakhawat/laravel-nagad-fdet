# Nagad Payment Gateway (Freelancer Digital Expert Team)

Laravel package for integrating the Nagad Modified Payment Gateway (via https://nagad.banglaqr.online).

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require theshakhawat/laravel-nagad-fdet
```

The package supports **Laravel Auto-Discovery**.

Run the all-in-one setup command:

```bash
php artisan nagad:install
```

### What `php artisan nagad:install` does:
1. Publishes `config/nagad.php`.
2. Automatically appends `NAGAD_API_KEY` and `NAGAD_BASE_URL` to your `.env` file.
3. Copies a ready-to-use demo checkout view to `resources/views/nagad/pay.blade.php`.
4. Copies `NagadPaymentController.php` to your `app/Http/Controllers/`.
5. Appends all necessary Nagad routes to your `routes/web.php`.

---

## ⚙️ Configuration

Open your `.env` file and insert your Nagad Merchant API key:

```env
NAGAD_API_KEY="np_live_your_48_character_api_key_here"
NAGAD_BASE_URL="https://nagad.banglaqr.online"
```

> **Note:** Set your website Callback URL in the Nagad Merchant Portal to:  
> `https://yourdomain.com/nagad/payment/callback`

---

## 🛍️ Demo Checkout Page

Start your Laravel development server:

```bash
php artisan serve
```

Visit the demo checkout page in your browser:

👉 **`http://127.0.0.1:8000/nagad/checkout`**

This page includes dummy products and a **Proceed to Payment with Nagad** button. Clicking it initiates the payment and redirects to the official Nagad payment portal.

---

## 🛠️ Usage with Facade

```php
use Theshakhawat\NagadFdet\Facades\NagadFdet;

// Initiate Payment
$response = NagadFdet::initiatePayment($amount, $orderId, $additionalInfo);
return redirect()->away($response['paymentRedirectUrl']);

// Query Authoritative Verification
$verification = NagadFdet::verifyPayment($paymentRefId);

// Check if successful
if (NagadFdet::isSuccess($verification)) {
    // Payment Successful!
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
