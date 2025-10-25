<?php
// ==================== 🧭 ADMIN DASHBOARD ====================
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../includes/db.php';

try {
    // ==================== 📊 GENERAL STATS ====================
    $totalSites = (int) $pdo->query("SELECT COUNT(*) FROM sites")->fetchColumn();
    $activeSites = (int) $pdo->query("SELECT COUNT(*) FROM sites WHERE status = 'active'")->fetchColumn();
    $inactiveSites = (int) $pdo->query("SELECT COUNT(*) FROM sites WHERE status = 'inactive'")->fetchColumn();
    $totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // ==================== 📊 ORDER STATS ====================
    $orderStats = $pdo->query("
        SELECT
            COUNT(*) AS total,
            SUM(payment_status = 'paid') AS completed,
            SUM(payment_status = 'pending') AS in_progress,
            SUM(payment_status = 'failed') AS cancelled
        FROM orders
    ")->fetch(PDO::FETCH_ASSOC);

    // ==================== 📋 RECENT ORDERS ====================
    $recentOrders = $pdo->query("
        SELECT 
            o.order_identifier,
            u.name AS buyer_name,
            o.final_total,
            o.payment_status,
            o.order_status,
            o.created_at
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== 📈 MONTHLY ORDER GROWTH ====================
    $growthQuery = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%b') AS month,
            COUNT(*) AS count
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY MIN(created_at)
    ");
    $growthData = $growthQuery->fetchAll(PDO::FETCH_ASSOC);

    $months = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthKey = date('M', strtotime("-$i month"));
        $months[$monthKey] = 0;
    }
    foreach ($growthData as $row) {
        $months[$row['month']] = (int)$row['count'];
    }

    $growth = [
        'labels' => array_keys($months),
        'values' => array_values($months)
    ];
} catch (PDOException $e) {
    echo "<div class='text-red-600 p-4 bg-red-50 rounded'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!-- ==================== 💻 DASHBOARD CONTENT ==================== -->
<div class="mb-6">
  <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
  <p class="text-gray-500">Quick snapshot of the platform’s activity.</p>
</div>


<!-- 🧮 MAIN STATS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <!-- Total Sites -->
  <div class="bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-md transition-all rounded-2xl p-6 border border-blue-200">
    <p class="text-blue-800 font-semibold uppercase tracking-wide text-sm mb-2">Total Sites</p>
    <h3 class="text-4xl font-extrabold text-blue-900"><?= $totalSites ?></h3>
  </div>

  <!-- Inactive Sites -->
  <div class="bg-gradient-to-br from-red-50 to-red-100 hover:shadow-md transition-all rounded-2xl p-6 border border-red-200">
    <p class="text-red-800 font-semibold uppercase tracking-wide text-sm mb-2">Inactive Sites</p>
    <h3 class="text-4xl font-extrabold text-red-900"><?= $inactiveSites ?></h3>
  </div>

  <!-- Active Sites -->
  <div class="bg-gradient-to-br from-green-50 to-green-100 hover:shadow-md transition-all rounded-2xl p-6 border border-green-200">
    <p class="text-green-800 font-semibold uppercase tracking-wide text-sm mb-2">Active Sites</p>
    <h3 class="text-4xl font-extrabold text-green-900"><?= $activeSites ?></h3>
  </div>

  

  <!-- Total Users -->
  <div class="bg-gradient-to-br from-purple-50 to-purple-100 hover:shadow-md transition-all rounded-2xl p-6 border border-purple-200">
    <p class="text-purple-800 font-semibold uppercase tracking-wide text-sm mb-2">Total Users</p>
    <h3 class="text-4xl font-extrabold text-purple-900"><?= $totalUsers ?></h3>
  </div>
</div>

<!-- 💵 ORDER STATS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <!-- Total Orders -->
  <div class="bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-md transition-all rounded-2xl p-6 border border-gray-200">
    <p class="text-gray-700 font-semibold uppercase tracking-wide text-sm mb-2">Total Orders</p>
    <h3 class="text-4xl font-extrabold text-gray-900"><?= $orderStats['total'] ?? 0 ?></h3>
  </div>

  <!-- Completed -->
  <div class="bg-gradient-to-br from-green-50 to-green-200 hover:shadow-md transition-all rounded-2xl p-6 border border-green-300">
    <p class="text-green-800 font-semibold uppercase tracking-wide text-sm mb-2">Completed Orders</p>
    <h3 class="text-4xl font-extrabold text-green-900"><?= $orderStats['completed'] ?? 0 ?></h3>
  </div>

  <!-- In Progress -->
  <div class="bg-gradient-to-br from-yellow-50 to-yellow-200 hover:shadow-md transition-all rounded-2xl p-6 border border-yellow-300">
    <p class="text-yellow-800 font-semibold uppercase tracking-wide text-sm mb-2">In Progress Orders</p>
    <h3 class="text-4xl font-extrabold text-yellow-900"><?= $orderStats['in_progress'] ?? 0 ?></h3>
  </div>

  <!-- Cancelled -->
  <div class="bg-gradient-to-br from-red-50 to-red-200 hover:shadow-md transition-all rounded-2xl p-6 border border-red-300">
    <p class="text-red-800 font-semibold uppercase tracking-wide text-sm mb-2">Cancelled Orders</p>
    <h3 class="text-4xl font-extrabold text-red-900"><?= $orderStats['cancelled'] ?? 0 ?></h3>
  </div>
</div>


<!-- 🧾 RECENT ORDERS -->
<!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
  <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Orders</h3>
  <table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left font-medium text-gray-600">Order ID</th>
        <th class="px-4 py-2 text-left font-medium text-gray-600">Buyer</th>
        <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
        <th class="px-4 py-2 text-right font-medium text-gray-600">Amount</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      <?php if (empty($recentOrders)): ?>
        <tr><td colspan="4" class="text-center py-4 text-gray-400">No recent orders.</td></tr>
      <?php else: ?>
        <?php foreach ($recentOrders as $order): ?>
          <tr>
            <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($order['order_identifier']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($order['buyer_name']) ?></td>
            <td class="px-4 py-2">
              <span class="px-2 py-1 text-xs rounded-full 
                <?= $order['payment_status'] === 'paid' ? 'bg-green-100 text-green-700' : 
                   ($order['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                   'bg-red-100 text-red-700') ?>">
                <?= ucfirst($order['payment_status']) ?>
              </span>
            </td>
            <td class="px-4 py-2 text-right font-semibold">$<?= number_format((float)$order['final_total'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div> -->

<!-- 📈 CHART -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
  <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Growth (Last 6 Months)</h3>
  <canvas id="ordersGrowthChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const ctx = document.getElementById("ordersGrowthChart");
  new Chart(ctx, {
    type: "line",
    data: {
      labels: <?= json_encode($growth['labels']) ?>,
      datasets: [{
        label: "Orders",
        data: <?= json_encode($growth['values']) ?>,
        borderColor: "#2563eb",
        backgroundColor: "rgba(37,99,235,0.1)",
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });
});
</script>
