<?php
session_start();

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

      <!-- ✅ Billing Details -->
      <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-6">
  <h2 class="text-xl font-semibold mb-4">Billing Details</h2>

  <div class="grid md:grid-cols-2 gap-4">

    <div>
      <label class="block text-sm font-medium mb-1">
        Company Name <span class="text-sm text-red-600">*</span>
      </label>
      <input 
        type="text" 
        name="billing_company" 
        required 
        placeholder="e.g. ABC Marketing Solutions" 
        class="w-full border rounded p-2 text-sm"
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
        class="w-full border rounded p-2 text-sm"
      >
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium mb-1">
        Street Address <span class="text-sm text-red-600">*</span>
      </label>
      <input 
        type="text" 
        name="billing_address" 
        required 
        placeholder="e.g. 123 Business Avenue, Suite 400" 
        class="w-full border rounded p-2 text-sm"
      >
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">
        City <span class="text-sm text-red-600">*</span>
      </label>
      <input 
        type="text" 
        name="billing_city" 
        required 
        placeholder="e.g. London" 
        class="w-full border rounded p-2 text-sm"
      >
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">
        Postal Code <span class="text-sm text-red-600">*</span>
      </label>
      <input 
        type="text" 
        name="billing_postal" 
        required 
        placeholder="e.g. SW1A 1AA" 
        class="w-full border rounded p-2 text-sm"
      >
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">
        Country <span class="text-sm text-red-600">*</span>
      </label>
      <input 
        type="text" 
        name="billing_country" 
        required 
        placeholder="e.g. United Kingdom" 
        class="w-full border rounded p-2 text-sm"
      >
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">
        Email Address <span class="text-sm text-red-600">*</span>
      </label>
      <input 
        type="email" 
        name="billing_email" 
        required 
        placeholder="e.g. accounts@abcmkt.com" 
        class="w-full border rounded p-2 text-sm"
      >
    </div>

  </div>
</div>


      <div class="flex justify-center items-center space-x-4 mt-6">
  <!-- Pay with Invoice -->
  <button 
    type="submit" 
    name="payment_method" 
    value="invoice" 
    class="bg-gray-700 text-white px-6 py-3 rounded hover:bg-gray-800 transition text-sm font-semibold"
  >
    Pay with Invoice (2–3 Days)
  </button>

    <!-- OR separator -->
  <div class="text-center text-gray-500 font-medium">OR</div>


  <!-- Pay with Wise -->
<a 
  id="wisePayLink"
  href="https://wise.com/pay/business/topurlzltd?amount=<?= $totals['total'] ?>&currency=EUR" 
  target="_blank" 
  class="bg-[#00B9A7] text-white px-6 py-3 rounded hover:bg-[#009e91] transition text-sm font-semibold"
>
  Pay with Wise (Faster Publish)
</a>

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

document.addEventListener("DOMContentLoaded", () => {
  const totalEl = document.getElementById("cart-total");
  const wiseLink = document.getElementById("wisePayLink");
  if (totalEl && wiseLink) {
    const amount = totalEl.textContent.trim();
    wiseLink.href = `https://wise.com/pay/business/topurlzltd?amount=${encodeURIComponent(amount)}&currency=EUR`;
  }
});

document.querySelectorAll('.add-content').forEach(chk => {
  chk.addEventListener('change', function() {    
    const index = this.dataset.index;
    const form = document.getElementById(`content-form-${index}`);
    const priceCell = this.closest('tr').querySelector('.final-price');
    const totalEl = document.getElementById('cart-total');

    const basePrice = parseFloat(priceCell.dataset.base);
    // parse current total (strip non numeric chars just in case)
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

    // --- NEW: update Wise link to reflect the new total ---
    const wiseLink = document.getElementById('wisePayLink');
    if (wiseLink) {
      // ensure amount has two decimals and no currency symbol
      const amount = currentTotal.toFixed(2);
      wiseLink.href = `https://wise.com/pay/business/topurlzltd?amount=${encodeURIComponent(amount)}&currency=EUR`;
    }
  });
});


// Countdown timers
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
</script>
</body>
</html>
