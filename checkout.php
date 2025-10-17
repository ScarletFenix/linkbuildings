<?php
session_start();

// Generate random order ID if not exists (6 characters)
if (!isset($_SESSION['current_order_id'])) {
    $_SESSION['current_order_id'] = 'ORD' . strtoupper(substr(uniqid(), -6));
}

// -------------------- FETCH ALL SITES --------------------
$sites = [];
$apiUrl = "http://localhost/linkbuildings/api/sites.php"; // API endpoint

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $json = json_decode($response, true);
    if (isset($json['data'])) {
        $sites = $json['data'];
    }
}

// -------------------- GET CART --------------------
$cart = $_SESSION['cart'] ?? [];

// -------------------- FUNCTIONS --------------------
function calculateTotals($cart) {
    $total_before = 0;
    $discount_amount_total = 0;
    $grand_total = 0;
    $messages = [];
    $now = new DateTime("now");

    foreach ($cart as &$item) {
        $price = (float)$item['price'];
        $has_discount = (int)($item['has_discount'] ?? 0);
        $discount_percent = (float)($item['discount_percent'] ?? 0);
        $discount_start = !empty($item['discount_start']) ? new DateTime($item['discount_start']) : null;
        $discount_end = !empty($item['discount_end']) ? new DateTime($item['discount_end']) : null;

        $status = "none";
        $discount_active = false;

        if ($has_discount && $discount_percent > 0 && $discount_start && $discount_end) {
            if ($now < $discount_start) {
                $status = "upcoming";
            } elseif ($now >= $discount_start && $now <= $discount_end) {
                $status = "active";
                $discount_active = true;
            } else {
                $status = "expired";
            }
        }

        $final_price = $price;
        $discount_amount = 0; 

        if ($discount_active) {
            $discount_amount = ($price * $discount_percent) / 100;
            $final_price -= $discount_amount;
        }

        $item['discount_status'] = $status;
        $item['discount_active'] = $discount_active;
        $item['discount_amount'] = $discount_amount;
        $item['final_price'] = $final_price;

        $total_before += $price;
        $discount_amount_total += $discount_amount;
        $grand_total += $final_price;
    }

    if ($discount_amount_total > 0) {
        $messages[] = "Discounts applied automatically where valid.";
    }

    return [
        "cart" => array_values($cart),
        "total_before" => number_format($total_before, 2),
        "discount_amount" => number_format($discount_amount_total, 2),
        "total" => number_format($grand_total, 2),
        "messages" => $messages
    ];
}

// -------------------- AJAX HANDLER --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $site_name = $_POST['site_name'] ?? '';
    $site_url = $_POST['site_url'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $discount_percent = (float)($_POST['discount_percent'] ?? 0);
    $discount_start = $_POST['discount_start'] ?? null;
    $discount_end = $_POST['discount_end'] ?? null;
    $has_discount = isset($_POST['has_discount']) ? (int)$_POST['has_discount'] : 0;

    $cart = $_SESSION['cart'] ?? [];

    if ($action === 'plus') {
        $cart[] = [
            'site_name' => $site_name,
            'site_url' => $site_url,
            'price' => $price,
            'discount_percent' => $discount_percent,
            'discount_start' => $discount_start,
            'discount_end' => $discount_end,
            'has_discount' => $has_discount
        ];
    } elseif ($action === 'remove_all') {
        foreach ($cart as $i => $item) {
            if (
                $item['site_name'] === $site_name &&
                $item['site_url'] === $site_url &&
                $item['price'] == $price
            ) {
                unset($cart[$i]);
                break;
            }
        }
    }

    $_SESSION['cart'] = $cart;
    echo json_encode(["success" => true] + calculateTotals($cart));
    exit;
}

// -------------------- PAGE LOAD --------------------
$totals = calculateTotals($cart);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .countdown { font-size: 0.85rem; font-weight: 600; color: #dc2626; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.2s ease-in-out; }
  </style>
</head>
<body class="bg-gray-100 py-10">
  <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-3xl font-bold text-center mb-2">Checkout</h1>
    <p class="text-center text-lg mb-6">Your Selected Sites</p>

    <form id="orderForm" method="POST" action="submit_order.php">
      <div class="overflow-x-auto mb-6">
        <table class="w-full border-collapse" id="cart-table">
          <thead>
            <tr class="bg-gray-800 text-white text-left">
              <th class="px-4 py-2">Site</th>
              <th class="px-4 py-2">Price (€)</th>
              <th class="px-4 py-2">Discount</th>
              <th class="px-4 py-2">Final (€)</th>
              <th class="px-4 py-2">Quantity</th>
              <th class="px-4 py-2">Add Content (€20)</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody id="cart-body">
            <?php foreach ($totals['cart'] as $index => $item): ?>
              <tr class="border-t align-top">
                <td class="px-4 py-2">
                  <input type="hidden" name="items[<?= $index ?>][site_name]" value="<?= htmlspecialchars($item['site_name']) ?>">
                  <input type="hidden" name="items[<?= $index ?>][site_url]" value="<?= htmlspecialchars($item['site_url']) ?>">
                  <input type="hidden" name="items[<?= $index ?>][price]" value="<?= $item['price'] ?>">
                  <div class="font-semibold"><?= htmlspecialchars($item['site_name']) ?></div>
                  <div class="text-sm text-gray-500"><?= htmlspecialchars($item['site_url']) ?></div>
                </td>

                <td class="px-4 py-2">€<?= number_format($item['price'], 2) ?></td>

                <td class="px-4 py-2 text-center">
                  <?php if ($item['discount_status'] === 'upcoming'): ?>
                    <div class="text-yellow-600 font-semibold">Upcoming</div>
                    <div class="text-sm text-gray-500">Starts: <?= date('M j, Y g:i A', strtotime($item['discount_start'])) ?></div>
                  <?php elseif ($item['discount_status'] === 'active'): ?>
                    <div class="text-green-700 font-semibold"><?= $item['discount_percent'] ?>% OFF</div>
                    <div class="text-xs text-gray-500">Ends: <?= date('M j, Y g:i A', strtotime($item['discount_end'])) ?></div>
                    <div class="countdown" data-end="<?= htmlspecialchars($item['discount_end']) ?>"></div>
                  <?php elseif ($item['discount_status'] === 'expired'): ?>
                    <div class="text-gray-500 font-semibold">Expired</div>
                  <?php else: ?>
                    <span class="text-gray-400">—</span>
                  <?php endif; ?>
                </td>

                <td class="px-4 py-2 text-center final-price" data-base="<?= $item['final_price'] ?>">€<?= number_format($item['final_price'], 2) ?></td>

                <td class="px-4 py-2 flex items-center space-x-2">
                  <button type="button" onclick="updateCart('remove_all','<?= $item['site_name'] ?>','<?= $item['site_url'] ?>',<?= $item['price'] ?>,<?= $item['discount_percent'] ?>,'<?= $item['discount_start'] ?>','<?= $item['discount_end'] ?>',<?= $item['has_discount'] ?>)" class="px-2 py-1 bg-gray-300 rounded">-</button>
                  <span>1</span>
                  <button type="button" onclick="updateCart('plus','<?= $item['site_name'] ?>','<?= $item['site_url'] ?>',<?= $item['price'] ?>,<?= $item['discount_percent'] ?>,'<?= $item['discount_start'] ?>','<?= $item['discount_end'] ?>',<?= $item['has_discount'] ?>)" class="px-2 py-1 bg-gray-300 rounded">+</button>
                </td>

                <td class="px-4 py-2 text-center">
                  <label class="flex justify-center items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="add-content h-4 w-4 text-blue-600 border-gray-300 rounded" data-index="<?= $index ?>">
                    <span class="text-sm">Add</span>
                  </label>
                </td>

                <td class="px-4 py-2">
                  <button type="button" onclick="updateCart('remove_all','<?= $item['site_name'] ?>','<?= $item['site_url'] ?>',<?= $item['price'] ?>,<?= $item['discount_percent'] ?>,'<?= $item['discount_start'] ?>','<?= $item['discount_end'] ?>',<?= $item['has_discount'] ?>)" class="bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                </td>
              </tr>

              <tr id="content-form-<?= $index ?>" class="hidden bg-gray-50">
                <td colspan="7" class="px-6 py-3">
                  <div class="grid md:grid-cols-1 gap-3 animate-fadeIn">
                    <label>Link Destination</label>
<input 
  type="text" 
  name="items[<?= $index ?>][linkDestination]" 
  placeholder="Enter the destination URL (e.g. https://example.com)" 
  class="border rounded p-2 w-full text-sm">

<label>Topic Suggestion</label>
<input 
  type="text" 
  name="items[<?= $index ?>][topicSuggestion]" 
  placeholder="Enter your topic idea (e.g. SEO best practices)" 
  class="border rounded p-2 w-full text-sm">

<label>Anchor</label>
<input 
  type="text" 
  name="items[<?= $index ?>][anchor]" 
  placeholder="Enter anchor text (e.g. digital marketing)" 
  class="border rounded p-2 w-full text-sm">

<label>Trust Link</label>
<input 
  type="text" 
  name="items[<?= $index ?>][trustLink]" 
  placeholder="Enter up to 2 trusted links (e.g. Wikipedia, Forbes)" 
  class="border rounded p-2 w-full text-sm">

                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <p class="text-md text-red-600 mt-2">Content is 600 words and you may add 1–2 trust links.</p>
      </div>

      <div id="discount-container" class="mb-4 space-y-2 text-center">
  <?php foreach ($totals['messages'] as $msg): ?>
    <div class="bg-[#e6fcf8] text-[#00bdaf] px-4 py-2 rounded w-full text-center font-semibold text-2xl">
      <?= $msg ?>
    </div>
  <?php endforeach; ?>
</div>

      <div class="text-right space-y-1 mb-6">
        <p class="text-lg font-semibold">Total before discount: €<span id="cart-total-before"><?= $totals['total_before'] ?></span></p>
        <p class="text-lg font-semibold">Discount: €<span id="cart-discount"><?= $totals['discount_amount'] ?></span></p>
        <p class="text-lg font-semibold">Final Total: €<span id="cart-total"><?= $totals['total'] ?></span></p>
      </div>

      <!-- ✅ Hidden inputs to send totals to submit_order.php -->
<input type="hidden" name="total_before" id="input-total-before" value="<?= $totals['total_before'] ?>">
<input type="hidden" name="discount_amount" id="input-discount" value="<?= $totals['discount_amount'] ?>">
<input type="hidden" name="final_total" id="input-total" value="<?= $totals['total'] ?>">
<input type="hidden" name="order_id" value="<?= $_SESSION['current_order_id'] ?>">

      <!-- ✅ Other Instructions -->
      <div class="mb-6">
        <label for="otherInstructions" class="block font-semibold mb-2">Other Instructions</label>
        <textarea 
          id="otherInstructions" 
          name="other_instructions" 
          rows="3" 
          placeholder="Add any specific notes or special requests for your order..."
          class="w-full border rounded p-3 text-sm"
        ></textarea>
      </div>

      <!-- ✅ Payment Method Selection -->
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

      <!-- ✅ Order ID Display -->
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-yellow-800 mb-2">Important Payment Note</h3>
        <p class="text-yellow-700 text-sm">
          Please include this Order ID in your payment description/notes: 
          <span class="font-bold text-lg"><?= $_SESSION['current_order_id'] ?></span>
        </p>
        <p class="text-yellow-600 text-xs mt-1">
          This helps us quickly identify and process your payment.
        </p>
      </div>

      <!-- ✅ Billing Details (Initially Hidden) -->
      <div id="billing-details" class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-6 hidden">
        <h2 class="text-xl font-semibold mb-4">Billing Details</h2>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">
              Company Name <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="text" 
              name="billing_company" 
              placeholder="e.g. ABC Marketing Solutions" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              VAT Number <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="text" 
              name="billing_vat" 
              placeholder="e.g. GB123456789" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">
              Street Address <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="text" 
              name="billing_address" 
              placeholder="e.g. 123 Business Avenue, Suite 400" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              City <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="text" 
              name="billing_city" 
              placeholder="e.g. London" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              Postal Code <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="text" 
              name="billing_postal" 
              placeholder="e.g. SW1A 1AA" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              Country <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="text" 
              name="billing_country" 
              placeholder="e.g. United Kingdom" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              Email Address <span class="text-sm text-red-600">*</span>
            </label>
            <input 
              type="email" 
              name="billing_email" 
              placeholder="e.g. accounts@abcmkt.com" 
              class="w-full border rounded p-2 text-sm billing-field"
            >
          </div>
        </div>
      </div>

      <!-- ✅ Wise Payment Details (Initially Hidden) -->
      <div id="wise-details" class="bg-green-50 p-5 rounded-lg border border-green-200 mb-6 hidden">
        <h2 class="text-xl font-semibold mb-4 text-green-800">Wise Payment Details</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <h3 class="font-semibold text-green-700 mb-3">Payment Link</h3>
            <a 
              href="https://wise.com/pay/business/topurlzltd" 
              target="_blank" 
              class="block bg-white p-4 rounded-lg border border-green-300 hover:bg-green-50 transition text-center"
            >
              <div class="text-green-600 font-semibold">Pay with Wise</div>
              <div class="text-sm text-gray-600 mt-1">Click to open Wise payment page</div>
            </a>
            
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
              <h4 class="font-semibold text-blue-800 text-sm mb-2">Payment Instructions:</h4>
              <ul class="text-xs text-blue-700 space-y-1">
                <li>• Click the "Pay with Wise" link above</li>
                <li>• Enter amount: <strong>€<span id="wise-amount"><?= $totals['total'] ?></span></strong></li>
                <li>• Include Order ID in payment description</li>
                <li>• Complete the payment process</li>
              </ul>
            </div>
          </div>
          
          <div class="text-center">
            <h3 class="font-semibold text-green-700 mb-3">QR Code</h3>
            <div class="bg-white p-4 rounded-lg border border-green-300 inline-block">
              <!-- Replace with your actual QR code image -->
              <img 
                src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://wise.com/pay/business/topurlzltd?amount=<?= $totals['total'] ?>&currency=EUR&order_id=<?= $_SESSION['current_order_id'] ?>" 
                alt="Wise Payment QR Code" 
                class="w-48 h-48 mx-auto"
              >
              <p class="text-xs text-gray-600 mt-2">Scan to pay with Wise</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ✅ Submit Order Button -->
      <div class="flex justify-center mt-6">
        <button 
          type="submit" 
          id="submit-order-btn"
          class="bg-blue-600 text-white px-8 py-3 rounded hover:bg-blue-700 transition text-lg font-semibold"
        >
          Submit Order
        </button>
      </div>
    </form>
  </div>

<script>
function updateCart(action, site_name, site_url, price, discount_percent, discount_start, discount_end, has_discount) {
  fetch(window.location.href, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=${encodeURIComponent(action)}&site_name=${encodeURIComponent(site_name)}&site_url=${encodeURIComponent(site_url)}&price=${encodeURIComponent(price)}&discount_percent=${encodeURIComponent(discount_percent)}&discount_start=${encodeURIComponent(discount_start)}&discount_end=${encodeURIComponent(discount_end)}&has_discount=${encodeURIComponent(has_discount)}`
  })
  .then(res => res.json())
  .then(data => { if (data.success) location.reload(); });
}

// ✅ Payment Method Selection Logic
document.addEventListener('DOMContentLoaded', function() {
  const paymentOptions = document.querySelectorAll('.payment-option');
  const billingDetails = document.getElementById('billing-details');
  const wiseDetails = document.getElementById('wise-details');
  const paymentError = document.getElementById('payment-error');
  const billingFields = document.querySelectorAll('.billing-field');
  
  paymentOptions.forEach(option => {
    option.addEventListener('click', function() {
      // Remove selected style from all options
      paymentOptions.forEach(opt => {
        opt.classList.remove('border-blue-500', 'bg-blue-50');
      });
      
      // Add selected style to clicked option
      this.classList.add('border-blue-500', 'bg-blue-50');
      
      // Check the radio button
      const radio = this.querySelector('input[type="radio"]');
      radio.checked = true;
      
      // Show/hide details based on selection
      if (radio.value === 'invoice') {
        billingDetails.classList.remove('hidden');
        wiseDetails.classList.add('hidden');
        // Add required attribute for invoice
        billingFields.forEach(field => {
          field.setAttribute('required', 'required');
        });
      } else {
        billingDetails.classList.add('hidden');
        wiseDetails.classList.remove('hidden');
        // Remove required attribute for Wise
        billingFields.forEach(field => {
          field.removeAttribute('required');
        });
        updateWiseDetails();
      }
      
      // Hide error message
      paymentError.classList.add('hidden');
    });
  });
  
  // Form validation
  const orderForm = document.getElementById('orderForm');
  orderForm.addEventListener('submit', function(e) {
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    
    if (!selectedPayment) {
      e.preventDefault();
      paymentError.classList.remove('hidden');
      paymentError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return false;
    }
    
    // If invoice is selected, validate billing details
    if (selectedPayment.value === 'invoice') {
      const billingInputs = billingDetails.querySelectorAll('.billing-field');
      let valid = true;
      
      billingInputs.forEach(input => {
        if (!input.value.trim()) {
          valid = false;
          input.classList.add('border-red-500');
        } else {
          input.classList.remove('border-red-500');
        }
      });
      
      if (!valid) {
        e.preventDefault();
        showToast('❌ Please fill all required billing details for invoice payment.', true);
        billingDetails.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
      }
    }
    
    // Update hidden totals before submission
    updateHiddenTotals();
  });

  // Update Wise details when total changes
  function updateWiseDetails() {
    const totalEl = document.getElementById('cart-total');
    const wiseAmount = document.getElementById('wise-amount');
    const qrCode = document.querySelector('#wise-details img');
    
    if (totalEl && wiseAmount) {
      const amount = totalEl.textContent.trim();
      wiseAmount.textContent = amount;
      
      // Update QR code with current amount
      if (qrCode) {
        const orderId = '<?= $_SESSION['current_order_id'] ?>';
        qrCode.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://wise.com/pay/business/topurlzltd?amount=${amount}&currency=EUR&order_id=${orderId}`;
      }
    }
  }

  // Initial Wise details setup
  updateWiseDetails();
});

// ✅ Keep hidden total inputs synced with displayed totals
function updateHiddenTotals() {
  const totalBefore = document.getElementById('cart-total-before')?.innerText.trim() || '0.00';
  const discount = document.getElementById('cart-discount')?.innerText.trim() || '0.00';
  const total = document.getElementById('cart-total')?.innerText.trim() || '0.00';

  document.getElementById('input-total-before').value = totalBefore;
  document.getElementById('input-discount').value = discount;
  document.getElementById('input-total').value = total;
}

// ✅ --- AJAX ORDER SUBMISSION (works for both payment methods) ---
document.addEventListener("DOMContentLoaded", () => {
  const orderForm = document.getElementById("orderForm");

  orderForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedPayment) {
      showToast('❌ Please select a payment method.', true);
      return;
    }

    const formData = new FormData(orderForm);

    // Disable submit button while processing
    const submitBtn = document.getElementById("submit-order-btn");
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing...";

    // Show loader overlay
    showLoader("Processing your order...");

    fetch("submit_order.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      hideLoader();
      if (data.success) {
        const paymentMethod = selectedPayment.value;
        let successMessage = `✅ ${data.message} (Order #${data.order_id})`;
        
        if (paymentMethod === 'wise') {
          successMessage += '\nYou can now proceed with Wise payment.';
        } else {
          successMessage += '\nWe will send you an invoice shortly.';
        }
        
        showToast(successMessage);
        
        // Clear cart and reset form
        orderForm.reset();
        document.getElementById("cart-body").innerHTML = `
          <tr><td colspan="7" class="text-center py-6 text-gray-500">
            Your cart is now empty.
          </td></tr>
        `;
        document.getElementById("cart-total-before").innerText = "0.00";
        document.getElementById("cart-discount").innerText = "0.00";
        document.getElementById("cart-total").innerText = "0.00";
        
        // Reset payment selection
        document.querySelectorAll('.payment-option').forEach(opt => {
          opt.classList.remove('border-blue-500', 'bg-blue-50');
        });
        document.getElementById('billing-details').classList.add('hidden');
        document.getElementById('wise-details').classList.add('hidden');
        
        // Remove required attributes
        document.querySelectorAll('.billing-field').forEach(field => {
          field.removeAttribute('required');
        });
        
        // Generate new order ID for next order
fetch('generate_new_order_id.php')
  .then(res => res.json())
  .then(data => {
    if (data.success && data.new_order_id) {
      const orderIdSpan = document.querySelector('.font-bold.text-lg');
      if (orderIdSpan) {
        orderIdSpan.textContent = data.new_order_id;
      }
    }
    setTimeout(() => {
      location.reload();
    }, 3000);
  });


      } else {
        showToast("❌ Failed to submit order: " + (data.message || "Unknown error"), true);
        submitBtn.disabled = false;
        submitBtn.textContent = "Submit Order";
      }
    })
    .catch(err => {
      hideLoader();
      console.error(err);
      showToast("❌ An unexpected error occurred while submitting the order.", true);
      submitBtn.disabled = false;
      submitBtn.textContent = "Submit Order";
    });
  });
});

// ✅ --- Add Content Checkbox Logic ---
document.querySelectorAll('.add-content').forEach(chk => {
  chk.addEventListener('change', function() {    
    const index = this.dataset.index;
    const form = document.getElementById(`content-form-${index}`);
    const priceCell = this.closest('tr').querySelector('.final-price');
    const totalEl = document.getElementById('cart-total');

    const basePrice = parseFloat(priceCell.dataset.base);
    let currentTotal = parseFloat(totalEl.innerHTML.replace(/[^0-9.-]+/g, '')) || 0;
    let newPrice = basePrice;

    if (this.checked) {
      form.classList.remove('hidden');
      newPrice += 20;
      currentTotal += 20;
    } else {
      form.classList.add('hidden');
      currentTotal -= 20;
    }

    priceCell.innerHTML = "€" + newPrice.toFixed(2);
    totalEl.innerHTML = currentTotal.toFixed(2);

    // Update Wise details if Wise is selected
    const wiseSelected = document.querySelector('input[name="payment_method"]:checked')?.value === 'wise';
    if (wiseSelected) {
      updateWiseDetails();
    }
  });
});

// ✅ --- Countdown timers ---
function startCountdowns() {
  const countdowns = document.querySelectorAll('.countdown');
  countdowns.forEach(el => {
    const end = new Date(el.dataset.end);
    const timer = setInterval(() => {
      const now = new Date();
      const diff = end - now;
      if (diff <= 0) {
        clearInterval(timer);
        el.textContent = "Expired"; 
        el.style.color = "#6b7280";
        setTimeout(() => window.location.reload(), 1000);
        return;
      }
      const hrs = Math.floor(diff / (1000 * 60 * 60));
      const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const secs = Math.floor((diff % (1000 * 60)) / 1000);
      el.textContent = `${hrs}h ${mins}m ${secs}s left`;
    }, 1000);
  });
}

document.addEventListener("DOMContentLoaded", startCountdowns);

// ✅ --- Helper Toast + Loader ---
function showToast(message, isError = false) {
  const toast = document.createElement("div");
  toast.textContent = message;
  toast.className = `fixed bottom-4 right-4 px-5 py-3 rounded shadow-lg text-white text-sm z-50 transition transform duration-300 whitespace-pre-line
    ${isError ? 'bg-red-600' : 'bg-green-600'}`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 5000);
}

function showLoader(text = "Loading...") {
  const overlay = document.createElement("div");
  overlay.id = "loaderOverlay";
  overlay.className = "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
  overlay.innerHTML = `<div class="bg-white p-5 rounded shadow-lg text-center">
    <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-blue-600 mx-auto mb-3"></div>
    <p class="text-gray-700 font-medium">${text}</p>
  </div>`;
  document.body.appendChild(overlay);
}

function hideLoader() {
  const overlay = document.getElementById("loaderOverlay");
  if (overlay) overlay.remove();
}

// Function to update Wise details
function updateWiseDetails() {
  const totalEl = document.getElementById('cart-total');
  const wiseAmount = document.getElementById('wise-amount');
  const qrCode = document.querySelector('#wise-details img');
  
  if (totalEl && wiseAmount) {
    const amount = totalEl.textContent.trim();
    wiseAmount.textContent = amount;
    
    // Update QR code with current amount
    if (qrCode) {
      const orderId = '<?= $_SESSION['current_order_id'] ?>';
      qrCode.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://wise.com/pay/business/topurlzltd?amount=${amount}&currency=EUR&order_id=${orderId}`;
    }
  }
}
</script>

</body>
</html>