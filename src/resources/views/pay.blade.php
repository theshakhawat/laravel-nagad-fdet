<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nagad Checkout - Demo</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .checkout-container {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #eef1f6;
        }
        .checkout-header {
            background: linear-gradient(135deg, #f05a28 0%, #e02c1b 100%);
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .checkout-header h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .checkout-header p {
            font-size: 13px;
            opacity: 0.9;
        }
        .checkout-body {
            padding: 24px;
        }
        .product-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fbfbfd;
            border: 1px solid #edf0f5;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 12px;
        }
        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .product-icon {
            width: 44px;
            height: 44px;
            background: #fff0eb;
            color: #f05a28;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .product-title {
            font-size: 15px;
            font-weight: 600;
            color: #222;
        }
        .product-sku {
            font-size: 12px;
            color: #888;
        }
        .product-price {
            font-size: 15px;
            font-weight: 700;
            color: #222;
        }
        .form-group {
            margin-top: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #f05a28;
        }
        .order-summary {
            margin-top: 22px;
            border-top: 1px dashed #dcdfe6;
            padding-top: 16px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 8px;
            color: #666;
        }
        .summary-row.total {
            font-size: 17px;
            font-weight: 700;
            color: #222;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #edf0f5;
        }
        .summary-row.total .total-amount {
            color: #f05a28;
        }
        .btn-pay {
            width: 100%;
            margin-top: 24px;
            background: linear-gradient(135deg, #f05a28 0%, #e02c1b 100%);
            color: #ffffff;
            border: none;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-pay:hover {
            opacity: 0.95;
        }
        .btn-pay:active {
            transform: scale(0.99);
        }
        .nagad-badge {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 14px;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="checkout-header">
        <h2>Demo Checkout</h2>
        <p>Complete your order securely with Nagad</p>
    </div>

    <div class="checkout-body">
        <!-- Demo Products List -->
        <div class="product-card">
            <div class="product-info">
                <div class="product-icon">👕</div>
                <div>
                    <div class="product-title">Premium Cotton T-Shirt</div>
                    <div class="product-sku">SKU: TSH-BLK-001 • Qty: 1</div>
                </div>
            </div>
            <div class="product-price">৳ 650.00</div>
        </div>

        <div class="product-card">
            <div class="product-info">
                <div class="product-icon">☕</div>
                <div>
                    <div class="product-title">Ceramic Coffee Mug</div>
                    <div class="product-sku">SKU: MUG-WHT-002 • Qty: 1</div>
                </div>
            </div>
            <div class="product-price">৳ 350.00</div>
        </div>

        <!-- Checkout Form -->
        <form action="{{ route('nagad.payment.initiate') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" class="form-control" value="John Doe" required>
            </div>

            <div class="form-group">
                <label for="customer_phone">Customer Phone (Nagad Number)</label>
                <input type="text" id="customer_phone" name="customer_phone" class="form-control" value="01700000000" required>
            </div>

            <!-- Hidden Fields for payment -->
            <input type="hidden" name="amount" value="1000.00">
            <input type="hidden" name="order_id" value="ORD{{ time() }}">
            <input type="hidden" name="product_name" value="Demo Cart Order">
            <input type="hidden" name="product_sku" value="CART-001">

            <!-- Summary Breakdown -->
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>৳ 1000.00</span>
                </div>
                <div class="summary-row">
                    <span>Delivery Charge</span>
                    <span style="color: #27ae60;">Free</span>
                </div>
                <div class="summary-row total">
                    <span>Total Amount</span>
                    <span class="total-amount">৳ 1000.00 BDT</span>
                </div>
            </div>

            <button type="submit" class="btn-pay">
                Proceed to Payment with Nagad
            </button>
        </form>

        <div class="nagad-badge">
            🔒 Secured by Nagad Payment Gateway (FDET)
        </div>
    </div>
</div>

</body>
</html>

