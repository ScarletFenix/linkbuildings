<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

try {
    // ✅ Require admin login
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }

    // --- 📊 Order Statistics ---
    $statsQuery = $pdo->query("
        SELECT
            COUNT(*) AS total,
            SUM(payment_status = 'paid') AS completed,
            SUM(payment_status = 'pending') AS in_progress,
            SUM(payment_status = 'failed') AS cancelled
        FROM orders
    ");
    $stats = $statsQuery->fetch(PDO::FETCH_ASSOC) ?: [
        'total' => 0,
        'completed' => 0,
        'in_progress' => 0,
        'cancelled' => 0
    ];

    // --- 📋 All Orders (with Buyer Info) ---
    $ordersQuery = $pdo->query("
        SELECT 
            o.order_identifier,
            u.name AS buyer_name,
            COALESCE(s.site_url, 'Unknown Site') AS site_url,
            o.created_at,
            o.payment_method,
            o.payment_status,
            o.order_status,
            o.final_total
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN sites s ON o.site_id = s.id
        ORDER BY o.created_at DESC
    ");
    $recentOrders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

    // --- 📈 Growth Chart (6 Months) ---
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

    // ✅ Fill missing months (so graph is consistent)
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

    // ✅ Send Final JSON
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)($stats['total'] ?? 0),
            'completed' => (int)($stats['completed'] ?? 0),
            'in_progress' => (int)($stats['in_progress'] ?? 0),
            'cancelled' => (int)($stats['cancelled'] ?? 0),
        ],
        'recentOrders' => $recentOrders,
        'growth' => $growth
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
