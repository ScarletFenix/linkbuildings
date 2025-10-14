<?php
session_start();

// Get cart from session
$cart = $_SESSION['cart'] ?? [];
$now = time();
$total = 0;

foreach ($cart as &$item) {
    $item['discount_active'] = false;
    $item['discounted_price'] = $item['price'];
    $item['discount_status'] = 'none';

    $start = !empty($item['discount_start']) ? strtotime($item['discount_start']) : null;
    $end   = !empty($item['discount_end']) ? strtotime($item['discount_end']) : null;

    if (
        isset($item['has_discount']) &&
        $item['has_discount'] == 1 &&
        !empty($item['discount_percent']) &&
        $start && $end
    ) {
        if ($now < $start) {
            $item['discount_status'] = 'upcoming';
        } elseif ($now >= $start && $now <= $end) {
            $item['discount_status'] = 'active';
            $item['discount_active'] = true;
            $item['discounted_price'] = $item['price'] - ($item['price'] * ($item['discount_percent'] / 100));
        } else {
            $item['discount_status'] = 'expired';
        }
    }

    $total += $item['discounted_price'];
}
unset($item);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .countdown { font-size: 0.85rem; font-weight: 600; color: #dc2626; margin-top: 3px; }
  </style>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-7xl mx-auto mt-10 mb-20">
  <div class="bg-white shadow-lg rounded-lg overflow-hidden">

    <div class="bg-gradient-to-r from-blue-600 to-green-500 px-6 py-4">
      <h1 class="text-xl font-bold text-white">Your Cart</h1>
    </div>

    <div class="p-6 overflow-x-auto">
      <?php if (empty($cart)): ?>
        <p class="text-gray-600 text-center py-10 text-lg">Your cart is empty.</p>
      <?php else: ?>
        <table class="min-w-full border border-gray-200 rounded text-sm">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="px-4 py-2">Site</th>
              <th class="px-4 py-2">Domain</th>
              <th class="px-4 py-2">Price (€)</th>
              <th class="px-4 py-2">Discount</th>
              <th class="px-4 py-2">Final Price (€)</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $id => $item): 
                $discount = $item['discount_percent'] ?? 0;
                $originalPrice = $item['price'];
                $finalPrice = $item['discounted_price'];
                $status = $item['discount_status'];
                $start = !empty($item['discount_start']) ? strtotime($item['discount_start']) : null;
                $end = !empty($item['discount_end']) ? strtotime($item['discount_end']) : null;
            ?>
              <tr class="border-t">
                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($item['site_name']) ?></td>
                <td class="px-4 py-2 text-blue-600 underline">
                  <a href="<?= htmlspecialchars($item['site_url']) ?>" target="_blank">
                    <?= htmlspecialchars($item['site_url']) ?>
                  </a>
                </td>

                <td class="px-4 py-2">
                  €<?= number_format($originalPrice, 2) ?>
                </td>

                <td class="px-4 py-2 text-center">
                  <?php if ($status === 'upcoming'): ?>
                    <div class="flex flex-col items-center">
                      <span class="text-yellow-600 font-semibold">Upcoming</span>
                      <span class="text-xs text-gray-500">Starts: <?= date('M j, Y g:i A', $start) ?></span>
                    </div>
                  <?php elseif ($status === 'active'): ?>
                    <div class="flex flex-col items-center">
                      <span class="bg-green-100 text-green-700 font-semibold px-2 py-1 rounded text-xs"><?= $discount ?>% OFF</span>
                      <span class="text-xs text-gray-500">Ends: <?= date('M j, Y g:i A', $end) ?></span>
                      <span id="countdown-<?= $id ?>" class="countdown" data-end="<?= date('Y-m-d H:i:s', $end) ?>"></span>
                    </div>
                  <?php elseif ($status === 'expired'): ?>
                    <div class="flex flex-col items-center">
                      <span class="text-gray-500 font-semibold">Expired</span>
                    </div>
                  <?php else: ?>
                    <span class="text-gray-500">—</span>
                  <?php endif; ?>
                </td>

                <td class="px-4 py-2 font-semibold text-green-600">
                  €<?= number_format($finalPrice, 2) ?>
                </td>

                <td class="px-4 py-2">
                  <a href="/linkbuildings/cart/remove.php?site_id=<?= $id ?>"  
                     class="text-red-600 hover:underline">Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="mt-4 flex justify-between items-center">
          <p class="text-lg font-semibold">Total: €<?= number_format($total, 2) ?></p>
          <a href="/linkbuildings/checkout.php" 
             class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-lg shadow">
             Proceed to Checkout
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".countdown").forEach(el => {
    const end = el.dataset.end;
    if (!end) return;
    const target = new Date(end).getTime();

    const timer = setInterval(() => {
      const now = new Date().getTime();
      const diff = target - now;

      if (diff <= 0) {
        clearInterval(timer);
        el.textContent = "Expired";
        el.style.color = "#6b7280";
        // Auto-refresh page to update PHP logic
        setTimeout(() => window.location.reload(), 1000);
        return;
      }

      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      el.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s left`;
    }, 1000);
  });
});
</script>

</body>
</html>
