<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../includes/db.php';

// Pagination & Search
$limit = 10;
$page = isset($_GET['page_no']) ? max(1, (int)$_GET['page_no']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = '';
$params = [];
if ($search) {
    $where = "WHERE order_identifier LIKE :search OR billing_company LIKE :search OR billing_email LIKE :search";
    $params[':search'] = "%$search%";
}

try {
    // Total orders
    $totalQuery = $pdo->prepare("SELECT COUNT(*) FROM orders $where");
    $totalQuery->execute($params);
    $totalOrders = (int)$totalQuery->fetchColumn();
    $totalPages = ceil($totalOrders / $limit);

    // Fetch orders
    $ordersQuery = $pdo->prepare("
        SELECT * 
        FROM orders
        $where
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $val) $ordersQuery->bindValue($key, $val);
    $ordersQuery->bindValue(':limit', $limit, PDO::PARAM_INT);
    $ordersQuery->bindValue(':offset', $offset, PDO::PARAM_INT);
    $ordersQuery->execute();
    $orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='text-red-600 p-4 bg-red-50 rounded'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<div class="p-6 space-y-6">
<h1 class="text-2xl font-bold text-gray-800 mb-4">All Orders</h1>

<!-- Search -->
<form method="get" class="mb-4 flex space-x-2">
    <input type="hidden" name="page" value="orders">
    <input type="text" name="search" placeholder="Search orders, company, or email..." value="<?= htmlspecialchars($search) ?>" class="border border-gray-300 rounded px-3 py-2 w-full sm:w-1/3">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
</form>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Order ID</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Company</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Email</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Payment Status</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Order Status</th>
                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        <?php if (empty($orders)): ?>
            <tr><td colspan="7" class="text-center py-6 text-gray-400">No orders found.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleDetails('order-<?= $order['id'] ?>')">
                    <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($order['order_identifier']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($order['billing_company'] ?? '-') ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($order['billing_email'] ?? '-') ?></td>
                    <td class="px-4 py-3"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            <?= $order['payment_status'] === 'paid' ? 'bg-green-100 text-green-700' : 
                               ($order['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                               'bg-red-100 text-red-700') ?>">
                            <?= ucfirst($order['payment_status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            <?= $order['order_status'] === 'Completed' ? 'bg-green-100 text-green-700' :
                               ($order['order_status'] === 'InProgress' ? 'bg-blue-100 text-blue-700' :
                               ($order['order_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-red-100 text-red-700')) ?>">
                            <?= ucfirst($order['order_status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">$<?= number_format((float)$order['final_total'], 2) ?></td>
                </tr>
                <tr id="order-<?= $order['id'] ?>" class="hidden bg-gray-50">
                    <td colspan="7" class="px-4 py-3 space-y-1">
                        <p><strong>Target URL:</strong> <?= htmlspecialchars($order['target_url'] ?? '-') ?></p>
                        <p><strong>Anchor Text:</strong> <?= htmlspecialchars($order['anchor_text'] ?? '-') ?></p>
                        <p><strong>Requirements:</strong> <?= htmlspecialchars($order['requirements'] ?? '-') ?></p>
                        <div class="mt-2 space-x-2">
                            <?php foreach(['pending','InProgress','Completed','Rejected'] as $status): ?>
                                <button onclick="updateOrderStatus(<?= $order['id'] ?>,'<?= $status ?>')" class="px-3 py-1 rounded text-white <?= $status==='Completed'?'bg-green-600 hover:bg-green-700':($status==='InProgress'?'bg-blue-600 hover:bg-blue-700':($status==='pending'?'bg-yellow-600 hover:bg-yellow-700':'bg-red-600 hover:bg-red-700')) ?>"><?= $status ?></button>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2">
    <?php for($i=1; $i<=$totalPages; $i++): ?>
        <a href="?page=orders&page_no=<?= $i ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 rounded <?= $i==$page?'bg-blue-600 text-white':'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
</div>

<script>
function toggleDetails(id) {
    document.getElementById(id).classList.toggle('hidden');
}

function updateOrderStatus(orderId, status) {
    if(!confirm(`Update order ${orderId} to "${status}"?`)) return;
    fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify({order_id: orderId, status: status})
    })
    .then(r=>r.json())
    .then(d=>{ if(d.success) location.reload(); else alert(d.message||'Failed'); })
    .catch(e=>alert('Error updating status.'));
}
</script>
