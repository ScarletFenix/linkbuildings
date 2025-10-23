<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('buyer');

$userName = $_SESSION['user_name'] ?? 'Buyer';
?>

<div class="space-y-6">
  <!-- 👋 Greeting Section -->
  <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl p-6 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-semibold mb-1">Welcome back, <?= htmlspecialchars($userName) ?> 👋</h2>
      <p class="text-blue-100 text-sm">Here’s what’s happening with your account today.</p>
    </div>
  </section>

  <!-- 📊 Stats Grid -->
  <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6" id="statsGrid">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="package" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">Total Orders</p>
        <p id="totalOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">Completed</p>
        <p id="completedOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="clock" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">In Progress</p>
        <p id="inProgressOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-red-100 text-red-600 rounded-lg"><i data-lucide="x-circle" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">Cancelled</p>
        <p id="cancelledOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>
  </section>

  <!-- 📋 Recent Orders -->
  <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
      <a href="#" data-page="orders" class="text-blue-600 hover:underline text-sm">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs uppercase bg-gray-50 text-gray-500">
          <tr>
            <th class="px-4 py-3">Order ID</th>
            <th class="px-4 py-3">Website</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Amount</th>
          </tr>
        </thead>
        <tbody id="recentOrdersBody">
          <tr>
            <td colspan="5" class="text-center py-4 text-gray-400">Loading recent orders...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</div>

<!-- <script src="../assets/js/dashboard.js"></script> -->
 <script src="<?= rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') ?>/linkbuildings/buyer/assets/js/dashboard.js"></script>
