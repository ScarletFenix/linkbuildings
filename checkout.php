<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: /linkbuildings/cart/cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-5xl mx-auto mt-10 mb-20">
  <div class="bg-white shadow-lg rounded-lg overflow-hidden">
    
    <div class="bg-gradient-to-r from-green-600 to-blue-500 px-6 py-4">
      <h1 class="text-xl font-bold text-white">Checkout</h1>
    </div>

    <div class="p-6">
      <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
      <ul class="space-y-2">
        <?php 
          $total = 0;
          foreach ($cart as $item): 
            $total += $item['price'];
        ?>
          <li class="flex justify-between">
            <span><?= htmlspecialchars($item['site_name']) ?> (<?= htmlspecialchars($item['site_url']) ?>)</span>
            <span>€<?= number_format($item['price'], 2) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="mt-4 text-lg font-semibold">Total: €<?= number_format($total, 2) ?></p>

      <form action="/linkbuildings/cart/place_order.php" method="POST" class="mt-6 space-y-4">
        <div>
          <label class="block mb-1 font-medium">Payment Method</label>
          <select name="payment_method" class="w-full border rounded px-3 py-2">
            <option value="bank">Bank Transfer</option>
            <option value="easypaisa">Easypaisa</option>
            <option value="jazzcash">JazzCash</option>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-medium">Instructions</label>
          <textarea name="instructions" rows="4" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded">
          Confirm Order
        </button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
