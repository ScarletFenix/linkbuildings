<?php
session_start();


// Get cart from session
$cart = $_SESSION['cart'] ?? [];

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-5xl mx-auto mt-10 mb-20">
  <div class="bg-white shadow-lg rounded-lg overflow-hidden">

    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-green-500 px-6 py-4">
      <h1 class="text-xl font-bold text-white">Your Cart</h1>
    </div>

    <!-- Cart Body -->
    <div class="p-6">
      <?php if (empty($cart)): ?>
        <p class="text-gray-600 text-center py-10 text-lg">Your cart is empty.</p>
      <?php else: ?>
        <table class="w-full border border-gray-200 rounded text-sm">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="px-4 py-2">Site</th>
              <th class="px-4 py-2">Domain</th>
              <th class="px-4 py-2">Price (€)</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $id => $item): ?>
              <tr class="border-t">
                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($item['site_name']) ?></td>
                <td class="px-4 py-2 text-blue-600 underline">
                  <a href="<?= htmlspecialchars($item['site_url']) ?>" target="_blank">
                    <?= htmlspecialchars($item['site_url']) ?>
                  </a>
                </td>
                <td class="px-4 py-2">€<?= number_format($item['price'], 2) ?></td>
                <td class="px-4 py-2">
                  <a href="/linkbuildings/cart/remove.php?site_id=<?= $id ?>"  
                     class="text-red-600 hover:underline">Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Totals -->
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

</body>
</html>
