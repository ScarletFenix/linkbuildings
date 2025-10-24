<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireRole('buyer');

// Generate random order ID if not exists
if (!isset($_SESSION['current_order_id'])) {
    $_SESSION['current_order_id'] = 'ORD' . strtoupper(substr(uniqid(), -6));
}

// -------------------- GET SITE FROM URL PARAMETER --------------------
$siteId = $_GET['site_id'] ?? null;

if (!$siteId) {
    header("Location: /linkbuildings/buyer/dashboard.php");
    exit;
}

// -------------------- FETCH SITE FROM DATABASE --------------------
try {
    $stmt = $pdo->prepare("
        SELECT 
            id,
            site_name,
            site_url,
            price,
            dr,
            traffic,
            backlinks,
            niche,
            description,
            country
        FROM sites 
        WHERE id = ?
    ");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$site) {
        die("Site not found");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// -------------------- CALCULATE PRICING --------------------
$totalBefore = (float)$site['price'];
$finalTotal = $totalBefore;
$totalBeforeFormatted = number_format($totalBefore, 2);
$finalTotalFormatted = number_format($finalTotal, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - <?= htmlspecialchars($site['site_name']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.2s ease-in-out; }
  </style>
</head>
<body class="bg-gray-100 py-10">
  <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-3xl font-bold text-center mb-2">Checkout</h1>
    <p class="text-center text-lg mb-6">Your Selected Site</p>

    <form id="orderForm" method="POST" action="submit_order.php">
      <div class="overflow-x-auto mb-6">
        <table class="w-full border-collapse" id="cart-table">
          <thead>
            <tr class="bg-gray-800 text-white text-left">
              <th class="px-4 py-2">Site</th>
              <th class="px-4 py-2">Price (€)</th>
            </tr>
          </thead>
          <tbody id="cart-body">
            <tr class="border-t align-top">
              <td class="px-4 py-2">
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" name="site_url" value="<?= htmlspecialchars($site['site_url']) ?>">
                <input type="hidden" name="price" value="<?= $site['price'] ?>">

                <div class="text-sm font-bold text-blue-500">
                  <a href="<?= htmlspecialchars($site['site_url']) ?>" target="_blank" rel="noopener noreferrer">
                    <?= htmlspecialchars($site['site_url']) ?>
                  </a>
                </div>
              </td>

              <td class="px-4 py-2">€<?= number_format($site['price'], 2) ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Totals -->
      <div class="text-right space-y-1 mb-6">
        <p class="text-lg font-semibold">Total: €<span id="cart-total"><?= $finalTotalFormatted ?></span></p>
      </div>

      <!-- Hidden Inputs -->
      <input type="hidden" name="total_before" id="input-total-before" value="<?= $totalBeforeFormatted ?>">
      <input type="hidden" name="final_total" id="input-total" value="<?= $finalTotalFormatted ?>">
      <input type="hidden" name="order_id" value="<?= $_SESSION['current_order_id'] ?>">

      <!-- Target URL -->
      <div class="mb-6">
        <label for="targetURL" class="block font-semibold mb-2">Target URL <span class="text-red-600">*</span></label>
        <input type="text" id="targetURL" name="target_url" required placeholder="Enter the URL where the link should point (e.g. https://example.com/page)" class="w-full border rounded p-3 text-sm">
      </div>

      <!-- Anchor Text -->
      <div class="mb-6">
        <label for="anchorText" class="block font-semibold mb-2">Anchor Text <span class="text-red-600">*</span></label>
        <input type="text" id="anchorText" name="anchor_text" required placeholder="Enter the anchor text (e.g. digital marketing services)" class="w-full border rounded p-3 text-sm">
      </div>

      <!-- Requirements -->
      <div class="mb-6">
        <label for="requirements" class="block font-semibold mb-2">Requirements / Notes</label>
        <textarea id="requirements" name="requirements" rows="3" placeholder="Add any specific notes or special requests for your order..." class="w-full border rounded p-3 text-sm"></textarea>
      </div>

      <!-- Payment Method -->
      <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="border rounded-lg p-4 cursor-pointer payment-option" data-method="invoice">
            <label class="flex items-center space-x-3 cursor-pointer">
              <input type="radio" name="payment_method" value="invoice" class="h-4 w-4 text-blue-600">
              <span class="font-semibold">Pay with Invoice (2–3 Days)</span>
            </label>
            <p class="text-sm text-gray-600 mt-2">We'll send you an invoice. Processing may take 2-3 days.</p>
          </div>
          
          <div class="border rounded-lg p-4 cursor-pointer payment-option" data-method="wise">
            <label class="flex items-center space-x-3 cursor-pointer">
              <input type="radio" name="payment_method" value="wise" class="h-4 w-4 text-blue-600">
              <span class="font-semibold">Pay with Wise (Faster Publish)</span>
            </label>
            <p class="text-sm text-gray-600 mt-2">Instant payment for faster order processing and publishing.</p>
          </div>
        </div>
        <div id="payment-error" class="text-red-600 text-sm mt-2 hidden">Please select a payment method.</div>
      </div>

      <!-- Order ID Note -->
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-yellow-800 mb-2">Important Payment Note</h3>
        <p class="text-yellow-700 text-sm">
          Please include this Order ID in your payment description: 
          <span class="font-bold text-lg"><?= $_SESSION['current_order_id'] ?></span>
        </p>
      </div>

      <!-- Billing -->
      <div id="billing-details" class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-6 hidden">
        <h2 class="text-xl font-semibold mb-4">Billing Details</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Company Name *</label>
            <input type="text" name="billing_company" class="w-full border rounded p-2 text-sm billing-field">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">VAT Number *</label>
            <input type="text" name="billing_vat" class="w-full border rounded p-2 text-sm billing-field">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Street Address *</label>
            <input type="text" name="billing_address" class="w-full border rounded p-2 text-sm billing-field">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">City *</label>
            <input type="text" name="billing_city" class="w-full border rounded p-2 text-sm billing-field">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Postal Code *</label>
            <input type="text" name="billing_postal" class="w-full border rounded p-2 text-sm billing-field">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Country *</label>
            <input type="text" name="billing_country" class="w-full border rounded p-2 text-sm billing-field">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email *</label>
            <input type="email" name="billing_email" class="w-full border rounded p-2 text-sm billing-field">
          </div>
        </div>
      </div>

      <!-- Wise Payment -->
      <div id="wise-details" class="bg-green-50 p-5 rounded-lg border border-green-200 mb-6 hidden">
        <h2 class="text-xl font-semibold mb-4 text-green-800">Wise Payment Details</h2>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <h3 class="font-semibold text-green-700 mb-3">Payment Link</h3>
            <a href="https://wise.com/pay/business/topurlzltd" target="_blank" class="block bg-white p-4 rounded-lg border border-green-300 hover:bg-green-50 text-center">
              <div class="text-green-600 font-semibold">Pay with Wise</div>
            </a>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
              <h4 class="font-semibold text-blue-800 text-sm mb-2">Instructions:</h4>
              <ul class="text-xs text-blue-700 space-y-1">
                <li>• Click the link above</li>
                <li>• Enter amount: <strong>€<span id="wise-amount"><?= $finalTotalFormatted ?></span></strong></li>
                <li>• Include Order ID in notes</li>
              </ul>
            </div>
          </div>
          <div class="text-center">
            <h3 class="font-semibold text-green-700 mb-3">QR Code</h3>
            <div class="bg-white p-4 rounded-lg border border-green-300 inline-block">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://wise.com/pay/business/topurlzltd?amount=<?= $finalTotalFormatted ?>&currency=EUR&order_id=<?= $_SESSION['current_order_id'] ?>" alt="Wise Payment QR Code" class="w-48 h-48 mx-auto">
            </div>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex justify-center mt-6">
        <button type="submit" id="submit-order-btn" class="bg-blue-600 text-white px-8 py-3 rounded hover:bg-blue-700 transition text-lg font-semibold">
          Submit Order
        </button>
      </div>
    </form>
  </div>

<script>
// ✅ Payment Method Logic
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    const billingDetails = document.getElementById('billing-details');
    const wiseDetails = document.getElementById('wise-details');
    const billingFields = document.querySelectorAll('.billing-field');

    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            paymentOptions.forEach(o => o.classList.remove('border-blue-500', 'bg-blue-50'));
            this.classList.add('border-blue-500', 'bg-blue-50');
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            if (radio.value === 'invoice') {
                billingDetails.classList.remove('hidden');
                wiseDetails.classList.add('hidden');
                billingFields.forEach(f => f.required = true);
            } else {
                billingDetails.classList.add('hidden');
                wiseDetails.classList.remove('hidden');
                billingFields.forEach(f => f.required = false);
            }
        });
    });

    // ✅ AJAX form submission
    const orderForm = document.getElementById('orderForm');
    orderForm.addEventListener('submit', function(e) {
        e.preventDefault(); // prevent normal submission
        const formData = new FormData(orderForm);

        fetch('submit_order.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Order submitted successfully! Order ID: ' + data.order_id);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Unexpected error occurred.');
        });
    });
});
</script>
</body>
</html>
