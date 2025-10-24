<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('buyer');

$userName = $_SESSION['user_name'] ?? 'Buyer';
?>

<div class="space-y-6">
  <!-- 🧾 Page Header -->
  <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl p-6 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-semibold mb-1">My Orders</h2>
      <p class="text-blue-100 text-sm">Track and manage all your orders here.</p>
    </div>
  </section>

  <!-- 📋 Orders Table -->
  <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800">Orders Overview</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs uppercase bg-gray-50 text-gray-500">
          <tr>
            <th class="px-4 py-3">Order ID</th>
            <th class="px-4 py-3">Website</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Payment Method</th>
            <th class="px-4 py-3">Payment Status</th>
            <th class="px-4 py-3">Order Status</th>
            <th class="px-4 py-3 text-right">Amount</th>
          </tr>
        </thead>
        <tbody id="ordersTableBody">
          <tr>
            <td colspan="7" class="text-center py-4 text-gray-400">Loading orders...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</div>

<!-- ✅ Linked JS file -->
<script src="<?= rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') ?>/linkbuildings/buyer/assets/js/orders.js"></script>
