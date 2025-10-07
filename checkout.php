<?php
session_start();

// Get current cart
$cart = $_SESSION['cart'] ?? [];

// -------------------- FUNCTIONS --------------------
function calculateTotals($cart) {
    $discountable_total = 0;
    $non_discount_total = 0;
    $discount_links = 0;

    foreach ($cart as $item) {
        if ($item['has_discount']) {
            $discountable_total += $item['price'];
            $discount_links++; // count duplicates as separate links
        } else {
            $non_discount_total += $item['price'];
        }
    }

    // Bulk discount tiers
    $discount_percent = 0;
    if ($discount_links >= 25) {
        $discount_percent = 25;
    } elseif ($discount_links >= 20) {
        $discount_percent = 20;
    } elseif ($discount_links >= 15) {
        $discount_percent = 15;
    } elseif ($discount_links >= 10) {
        $discount_percent = 10;
    } elseif ($discount_links >= 5) {
        $discount_percent = 5;
    }

    $discount_amount = ($discountable_total * $discount_percent) / 100;
    $grand_total = $discountable_total - $discount_amount + $non_discount_total;
    $total_before = $discountable_total + $non_discount_total;

    // Messages
    $messages = [];
    if ($discount_percent > 0) {
        $messages[] = "✅ {$discount_percent}% discount applied on Bulk items.";
    }

    $next_thresholds = [5, 10, 15, 20, 25];
    foreach ($next_thresholds as $t) {
        if ($discount_links < $t) {
            $messages[] = "You're " . ($t - $discount_links) . " Bulk links away from {$t}% discount!";
            break;
        }
    }

    return [
        "cart" => array_values($cart),
        "total_before" => number_format($total_before, 2),
        "discount_amount" => number_format($discount_amount, 2),
        "total" => number_format($grand_total, 2),
        "discount_percent" => $discount_percent,
        "messages" => $messages
    ];
}

// -------------------- AJAX HANDLER --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $site_name = $_POST['site_name'] ?? '';
    $site_url = $_POST['site_url'] ?? '';
    $price = (float) ($_POST['price'] ?? 0);
    $has_discount = (int) ($_POST['has_discount'] ?? 0);

    if ($action === 'plus') {
        // Add a duplicate entry
        $cart[] = [
            'site_name' => $site_name,
            'site_url' => $site_url,
            'price' => $price,
            'has_discount' => $has_discount
        ];
    } elseif ($action === 'remove_all') {
        // Remove the first matching entry
        foreach ($cart as $i => $item) {
            if (
                $item['site_name'] === $site_name &&
                $item['site_url'] === $site_url &&
                $item['price'] == $price &&
                $item['has_discount'] == $has_discount
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
</head>
<body class="bg-gray-100 py-10">
  <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-3xl font-bold text-center mb-2">Checkout</h1>
    <p class="text-center text-lg mb-6">Your Selected Sites</p>

    <div class="overflow-x-auto mb-6">
      <table class="w-full border-collapse" id="cart-table">
        <thead>
          <tr class="bg-gray-800 text-white text-left">
            <th class="px-4 py-2">Site</th>
            <th class="px-4 py-2">Price (€)</th>
            <th class="px-4 py-2">Quantity</th>
            <th class="px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody id="cart-body">
          <?php foreach ($totals['cart'] as $item): ?>
            <tr class="border-t">
              <td class="px-4 py-2">
                <?= htmlspecialchars($item['site_name']) ?>
                <?php if ($item['has_discount']): ?>
                  <span class="ml-2 bg-teal-200 text-teal-800 px-2 py-1 text-xs rounded">Bulk</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2">€<?= number_format($item['price'], 2) ?></td>
              <td class="px-4 py-2 flex items-center space-x-2">
                <button onclick="updateCart('remove_all','<?= $item['site_name'] ?>','<?= $item['site_url'] ?>',<?= $item['price'] ?>,<?= $item['has_discount'] ?>)" class="px-2 py-1 bg-gray-300 rounded">-</button>
                <span>1</span>
                <button onclick="updateCart('plus','<?= $item['site_name'] ?>','<?= $item['site_url'] ?>',<?= $item['price'] ?>,<?= $item['has_discount'] ?>)" class="px-2 py-1 bg-gray-300 rounded">+</button>
              </td>
              <td class="px-4 py-2">
                <button onclick="updateCart('remove_all','<?= $item['site_name'] ?>','<?= $item['site_url'] ?>',<?= $item['price'] ?>,<?= $item['has_discount'] ?>)" class="bg-red-500 text-white px-3 py-1 rounded">Remove</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Discount messages -->
    <div id="discount-container" class="mb-4 space-y-2">
      <?php foreach ($totals['messages'] as $msg): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded"><?= $msg ?></div>
      <?php endforeach; ?>
    </div>

    <div class="text-right space-y-1 mb-6">
      <p class="text-lg font-semibold">Total before discount: €<span id="cart-total-before"><?= $totals['total_before'] ?></span></p>
      <p class="text-lg font-semibold">Discount: €<span id="cart-discount"><?= $totals['discount_amount'] ?></span></p>
      <p class="text-lg font-semibold">Final Total: €<span id="cart-total"><?= $totals['total'] ?></span></p>
    </div>

    <h2 class="text-xl font-bold mb-2">Order Instructions</h2>
    <textarea class="w-full border rounded p-3" rows="4" placeholder="Enter detailed order instructions, including URLs for links and any specific requirements."></textarea>
  </div>

<script>
function updateCart(action, site_name, site_url, price, has_discount) {
  fetch(window.location.href, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=${encodeURIComponent(action)}&site_name=${encodeURIComponent(site_name)}&site_url=${encodeURIComponent(site_url)}&price=${encodeURIComponent(price)}&has_discount=${encodeURIComponent(has_discount)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const tbody = document.getElementById("cart-body");
      tbody.innerHTML = "";
      data.cart.forEach(item => {
        tbody.innerHTML += `
          <tr class="border-t">
            <td class="px-4 py-2">${item.site_name} 
              ${item.has_discount == 1 ? '<span class="ml-2 bg-teal-200 text-teal-800 px-2 py-1 text-xs rounded">Bulk</span>' : ''}
            </td>
            <td class="px-4 py-2">€${parseFloat(item.price).toFixed(2)}</td>
            <td class="px-4 py-2 flex items-center space-x-2">
              <button onclick="updateCart('remove_all','${item.site_name}','${item.site_url}',${item.price},${item.has_discount})" class="px-2 py-1 bg-gray-300 rounded">-</button>
              <span>1</span>
              <button onclick="updateCart('plus','${item.site_name}','${item.site_url}',${item.price},${item.has_discount})" class="px-2 py-1 bg-gray-300 rounded">+</button>
            </td>
            <td class="px-4 py-2">
              <button onclick="updateCart('remove_all','${item.site_name}','${item.site_url}',${item.price},${item.has_discount})" class="bg-red-500 text-white px-3 py-1 rounded">Remove</button>
            </td>
          </tr>`;
      });

      document.getElementById("cart-total-before").innerText = data.total_before;
      document.getElementById("cart-discount").innerText = data.discount_amount;
      document.getElementById("cart-total").innerText = data.total;

      const container = document.getElementById("discount-container");
      container.innerHTML = "";
      data.messages.forEach(msg => {
        container.innerHTML += `<div class="bg-green-100 text-green-800 px-4 py-2 rounded">${msg}</div>`;
      });
    }
  });
}
</script>
</body>
</html>
